<?php

namespace App\Services;

use App\Enums\AssessmentStatus;
use App\Enums\AssessmentType;
use App\Enums\ParticipationStatus;
use App\Enums\ScoringStatus;
use App\Models\AssessmentCriterion;
use App\Models\AwardCategory;
use App\Models\Innovation;
use App\Models\InnovationAssessment;
use App\Models\InnovationAssessmentDetail;
use App\Models\Institution;
use App\Models\InstitutionAssessment;
use App\Models\InstitutionAssessmentDetail;
use App\Models\JurySubcategoryParticipation;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ScoringService
{
    /**
     * Open scoring for an award category.
     * Validates participants > 0, juries > 0, total criteria weight == 100.
     * Locks roster permanently in a transaction.
     */
    public function openScoring(AwardCategory $category, User $actor): void
    {
        if ($category->scoring_status !== ScoringStatus::Preparation) {
            throw new Exception('Subkategori sudah dibuka sebelumnya.');
        }

        // Validate participants exist
        $participantCount = $category->assessment_type === AssessmentType::Innovation
            ? $category->innovations()->count()
            : $category->institutions()->count();

        if ($participantCount === 0) {
            throw new Exception('Tidak ada peserta dalam subkategori ini.');
        }

        // Validate juries exist
        $juryCount = $category->participations()->count();
        if ($juryCount === 0) {
            throw new Exception('Tidak ada juri yang memilih subkategori ini.');
        }

        // Validate total criteria weight == 100
        $totalWeight = AssessmentCriterion::where('festival_id', $category->festival_id)
            ->where('assessment_type', $category->assessment_type)
            ->sum('weight');

        if ($totalWeight != 100) {
            throw new Exception("Total bobot kriteria harus 100. Saat ini: {$totalWeight}.");
        }

        DB::transaction(function () use ($category, $actor) {
            $oldStatus = $category->scoring_status;

            $category->update([
                'scoring_status' => ScoringStatus::Open,
                'opened_at' => now(),
                'opened_by' => $actor->id,
            ]);

            // Update all jury participations to in_progress
            $category->participations()->update(['status' => ParticipationStatus::InProgress]);

            AuditService::logChange(
                $actor,
                $category,
                'open_scoring',
                ['scoring_status' => $oldStatus->value],
                ['scoring_status' => ScoringStatus::Open->value]
            );
        });
    }

    /**
     * Close scoring for an award category.
     */
    public function closeScoring(AwardCategory $category, User $actor): void
    {
        if ($category->scoring_status !== ScoringStatus::Open) {
            throw new Exception('Subkategori belum dibuka atau sudah ditutup.');
        }

        DB::transaction(function () use ($category, $actor) {
            $oldStatus = $category->scoring_status;

            $category->update([
                'scoring_status' => ScoringStatus::Closed,
                'closed_at' => now(),
                'closed_by' => $actor->id,
            ]);

            AuditService::logChange(
                $actor,
                $category,
                'close_scoring',
                ['scoring_status' => $oldStatus->value],
                ['scoring_status' => ScoringStatus::Closed->value]
            );
        });
    }

    /**
     * Save assessment as draft (incomplete scores allowed).
     */
    public function saveDraft(
        JurySubcategoryParticipation $participation,
        Innovation|Institution $target,
        array $scores,
        User $actor
    ): InnovationAssessment|InstitutionAssessment {
        return $this->saveAssessment($participation, $target, $scores, $actor, false);
    }

    /**
     * Submit assessment (all criteria required, score 6-10).
     */
    public function submitAssessment(
        JurySubcategoryParticipation $participation,
        Innovation|Institution $target,
        array $scores,
        User $actor
    ): InnovationAssessment|InstitutionAssessment {
        $assessment = $this->saveAssessment($participation, $target, $scores, $actor, true);

        // Check if all objects in participation are completed
        $this->checkParticipationCompletion($participation);

        return $assessment;
    }

    /**
     * Lock an assessment.
     */
    public function lockAssessment(string $type, int $id, User $actor): void
    {
        $assessment = $this->findAssessment($type, $id);

        if ($assessment->status !== AssessmentStatus::Submitted) {
            throw new Exception('Hanya penilaian dengan status Terkirim yang dapat dikunci.');
        }

        DB::transaction(function () use ($assessment, $actor) {
            $oldStatus = $assessment->status;

            $assessment->update([
                'status' => AssessmentStatus::Locked,
                'locked_at' => now(),
            ]);

            AuditService::logChange(
                $actor,
                $assessment,
                'lock_assessment',
                ['status' => $oldStatus->value],
                ['status' => AssessmentStatus::Locked->value]
            );
        });
    }

    /**
     * Request revision for an assessment.
     */
    public function requestRevision(string $type, int $id, string $reason, User $actor): void
    {
        $assessment = $this->findAssessment($type, $id);

        if (!in_array($assessment->status, [AssessmentStatus::Submitted, AssessmentStatus::Locked])) {
            throw new Exception('Hanya penilaian dengan status Terkirim atau Terkunci yang dapat diminta revisi.');
        }

        DB::transaction(function () use ($assessment, $reason, $actor) {
            $oldStatus = $assessment->status;

            $assessment->update([
                'status' => AssessmentStatus::RevisionRequested,
            ]);

            AuditService::logChange(
                $actor,
                $assessment,
                'request_revision',
                ['status' => $oldStatus->value],
                ['status' => AssessmentStatus::RevisionRequested->value, 'reason' => $reason]
            );
        });
    }

    /**
     * Calculate weighted score for an assessment.
     * Formula: SUM(score * weight) / 100
     */
    public function calculateWeightedScore(InnovationAssessment|InstitutionAssessment $assessment): float
    {
        return $assessment->weightedTotal();
    }

    /**
     * Calculate average score for a target across all juries (monitoring in-flight).
     * Formula: total weighted scores / number of juries who assessed.
     *
     * CATATAN: berbeda dari RankingService::calculateAverageScore yang HANYA
     * menghitung penilaian Locked untuk hasil akhir. Method ini menghitung
     * Submitted + Locked untuk kebutuhan pemantauan berjalan.
     */
    public function calculateAverageScore(Innovation|Institution $target): float
    {
        $assessments = $target instanceof Innovation
            ? InnovationAssessment::where('innovation_id', $target->id)
                ->whereIn('status', [AssessmentStatus::Submitted, AssessmentStatus::Locked])
                ->get()
            : InstitutionAssessment::where('institution_id', $target->id)
                ->whereIn('status', [AssessmentStatus::Submitted, AssessmentStatus::Locked])
                ->get();

        if ($assessments->isEmpty()) {
            return 0.0;
        }

        $totalWeightedScore = $assessments->sum(fn ($a) => $this->calculateWeightedScore($a));

        return $totalWeightedScore / $assessments->count();
    }

    /**
     * Save assessment (internal helper).
     */
    private function saveAssessment(
        JurySubcategoryParticipation $participation,
        Innovation|Institution $target,
        array $scores,
        User $actor,
        bool $isSubmission
    ): InnovationAssessment|InstitutionAssessment {
        $isInnovation = $target instanceof Innovation;
        $assessmentType = $isInnovation ? AssessmentType::Innovation : AssessmentType::Institution;

        // Penilaian hanya boleh dilakukan selama periode scoring subkategori terbuka
        $category = $participation->awardCategory;
        if ($category->scoring_status !== ScoringStatus::Open) {
            throw new Exception('Periode penilaian untuk subkategori ini belum dibuka atau sudah ditutup.');
        }

        // Get criteria for this assessment type
        $criteria = AssessmentCriterion::where('festival_id', $participation->festival_id)
            ->where('assessment_type', $assessmentType)
            ->orderBy('sort_order')
            ->get();

        // Validate all criteria are present for submission
        if ($isSubmission) {
            $criteriaIds = $criteria->pluck('id')->toArray();
            $providedIds = array_keys($scores);

            $missing = array_diff($criteriaIds, $providedIds);
            if (!empty($missing)) {
                throw new Exception('Semua kriteria harus diisi.');
            }

            // Validate scores are between 6-10
            foreach ($scores as $criterionId => $score) {
                if (!is_int($score) || $score < 6 || $score > 10) {
                    throw new Exception('Nilai harus berupa bilangan bulat antara 6 sampai 10.');
                }
            }
        }

        return DB::transaction(function () use ($participation, $target, $scores, $actor, $isInnovation, $criteria, $isSubmission) {
            // Find or create assessment
            $assessment = $isInnovation
                ? InnovationAssessment::firstOrCreate(
                    [
                        'jury_subcategory_participation_id' => $participation->id,
                        'innovation_id' => $target->id,
                    ],
                    [
                        'festival_id' => $participation->festival_id,
                        'award_category_id' => $participation->award_category_id,
                        'status' => AssessmentStatus::Draft,
                    ]
                )
                : InstitutionAssessment::firstOrCreate(
                    [
                        'jury_subcategory_participation_id' => $participation->id,
                        'institution_id' => $target->id,
                    ],
                    [
                        'festival_id' => $participation->festival_id,
                        'award_category_id' => $participation->award_category_id,
                        'status' => AssessmentStatus::Draft,
                    ]
                );

            // Check if assessment can be modified
            if (in_array($assessment->status, [AssessmentStatus::Submitted, AssessmentStatus::Locked])) {
                if ($assessment->status === AssessmentStatus::Locked) {
                    throw new Exception('Penilaian sudah dikunci dan tidak dapat diubah.');
                }
                if ($assessment->status === AssessmentStatus::Submitted && !$isSubmission) {
                    throw new Exception('Penilaian sudah dikirim. Gunakan permintaan revisi untuk mengubah.');
                }
            }

            // Update status if submitting
            if ($isSubmission) {
                $assessment->update([
                    'status' => AssessmentStatus::Submitted,
                    'submitted_at' => now(),
                ]);
            }

            // Save detail scores
            foreach ($criteria as $criterion) {
                $score = $scores[$criterion->id] ?? null;

                if ($score === null) {
                    continue; // Skip empty scores in draft
                }

                if ($isInnovation) {
                    InnovationAssessmentDetail::updateOrCreate(
                        [
                            'innovation_assessment_id' => $assessment->id,
                            'assessment_criterion_id' => $criterion->id,
                        ],
                        [
                            'festival_id' => $participation->festival_id,
                            'assessment_type' => AssessmentType::Innovation,
                            'score' => $score,
                        ]
                    );
                } else {
                    InstitutionAssessmentDetail::updateOrCreate(
                        [
                            'institution_assessment_id' => $assessment->id,
                            'assessment_criterion_id' => $criterion->id,
                        ],
                        [
                            'festival_id' => $participation->festival_id,
                            'assessment_type' => AssessmentType::Institution,
                            'score' => $score,
                        ]
                    );
                }
            }

            // Log if submitting
            if ($isSubmission) {
                AuditService::logChange(
                    $actor,
                    $assessment,
                    'submit_assessment',
                    ['status' => AssessmentStatus::Draft->value],
                    ['status' => AssessmentStatus::Submitted->value]
                );
            }

            return $assessment;
        });
    }

    /**
     * Check if all objects in participation have been assessed.
     */
    private function checkParticipationCompletion(JurySubcategoryParticipation $participation): void
    {
        $category = $participation->awardCategory;
        $isInnovation = $category->assessment_type === AssessmentType::Innovation;

        // Get all objects to assess
        $totalObjects = $isInnovation
            ? $category->innovations()->count()
            : $category->institutions()->count();

        // Get submitted/locked assessments count (locked dihitung selesai karena
        // admin telah menerima penilaian tersebut)
        $submittedCount = $isInnovation
            ? $participation->innovationAssessments()
                ->whereIn('status', [AssessmentStatus::Submitted, AssessmentStatus::Locked])
                ->count()
            : $participation->institutionAssessments()
                ->whereIn('status', [AssessmentStatus::Submitted, AssessmentStatus::Locked])
                ->count();

        // Update participation completion if all assessed
        if ($submittedCount === $totalObjects && $totalObjects > 0) {
            $participation->update([
                'status' => ParticipationStatus::Completed,
                'completed_at' => now(),
            ]);
        }
    }

    /**
     * Find assessment by type and ID.
     */
    private function findAssessment(string $type, int $id): InnovationAssessment|InstitutionAssessment
    {
        if ($type === 'innovation') {
            return InnovationAssessment::findOrFail($id);
        }

        if ($type === 'institution') {
            return InstitutionAssessment::findOrFail($id);
        }

        throw new Exception('Tipe penilaian tidak valid. Gunakan "innovation" atau "institution".');
    }
}

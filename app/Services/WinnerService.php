<?php

namespace App\Services;

use App\Models\AwardCategory;
use App\Models\InnovationWinner;
use App\Models\InstitutionWinner;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class WinnerService
{
    public function __construct(
        protected RankingService $rankingService
    ) {}

    /**
     * Validate winner count and positions.
     *
     * @param array<int, array{target_type: string, target_id: int, position: int}> $winners
     *
     * @throws Exception
     */
    public function validateWinnerCount(AwardCategory $category, array $winners): void
    {
        // Check if count exceeds winner_count
        if (count($winners) > $category->winner_count) {
            throw new Exception("Jumlah pemenang (" . count($winners) . ") melebihi batas yang ditentukan ({$category->winner_count}).");
        }

        // Check for duplicate positions
        $positions = array_column($winners, 'position');
        $uniquePositions = array_unique($positions);
        
        if (count($positions) !== count($uniquePositions)) {
            throw new Exception('Posisi juara harus unik dalam satu subkategori.');
        }

        // Check for duplicate target_ids
        $targetIds = array_map(fn ($w) => $w['target_type'].'_'.$w['target_id'], $winners);
        $uniqueTargetIds = array_unique($targetIds);
        
        if (count($targetIds) !== count($uniqueTargetIds)) {
            throw new Exception('Satu peserta tidak dapat memenangkan lebih dari satu posisi.');
        }

        // Validate position values are >= 1
        foreach ($winners as $winner) {
            if ($winner['position'] < 1) {
                throw new Exception('Posisi juara harus bernilai 1 atau lebih besar.');
            }
        }
    }

    /**
     * Assign winners to an award category.
     *
     * @param array<int, array{target_type: string, target_id: int, position: int}> $winners
     */
    public function assignWinners(AwardCategory $category, array $winners, User $actor): void
    {
        $this->validateWinnerCount($category, $winners);

        DB::transaction(function () use ($category, $winners, $actor) {
            $isInnovation = $category->festivalCategory->assessment_type->value === 'innovation';
            
            // Delete existing winners
            if ($isInnovation) {
                $oldWinners = $category->innovationWinners()->get();
                $category->innovationWinners()->delete();
            } else {
                $oldWinners = $category->institutionWinners()->get();
                $category->institutionWinners()->delete();
            }

            // Log deletion of old winners
            foreach ($oldWinners ?? [] as $oldWinner) {
                AuditService::logChange(
                    $actor,
                    $oldWinner,
                    'delete_winner',
                    $oldWinner->toArray(),
                    []
                );
            }

            // Get rankings for score lookup
            $rankings = $this->rankingService->getRankings($category);

            // Insert new winners
            foreach ($winners as $winnerData) {
                $ranking = $rankings->firstWhere('id', $winnerData['target_id']);
                
                $winnerModel = $isInnovation ? new InnovationWinner() : new InstitutionWinner();
                
                $winnerModel->fill([
                    'festival_id' => $category->festival_id,
                    'award_category_id' => $category->id,
                    $isInnovation ? 'innovation_id' : 'institution_id' => $winnerData['target_id'],
                    'position' => $winnerData['position'],
                    'appointed_by' => $actor->id,
                    'appointed_at' => now(),
                    'total_score' => $ranking?->average_score ?? 0,
                    'notes' => $winnerData['notes'] ?? null,
                ]);
                
                $winnerModel->save();

                AuditService::logChange(
                    $actor,
                    $winnerModel,
                    'appoint_winner',
                    [],
                    $winnerModel->toArray()
                );
            }
        });
    }

    /**
     * Remove a winner.
     */
    public function removeWinner(InnovationWinner|InstitutionWinner $winner, User $actor): void
    {
        DB::transaction(function () use ($winner, $actor) {
            $oldData = $winner->toArray();
            
            $winner->delete();

            AuditService::logChange(
                $actor,
                $winner,
                'remove_winner',
                $oldData,
                []
            );
        });
    }
}

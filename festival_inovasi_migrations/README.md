# Festival Inovasi — Laravel Migration Draft

**Status:** Draft implementation package based on the approved ERD and PRD v1.0.  
**Database:** MySQL / InnoDB  
**Naming convention:** English, plural table names, `snake_case` columns.

This package contains 18 Laravel migration files. It creates the database structure only; it does not include models, controllers, services, policies, seeders, or frontend pages.

## 1. Contents

Migrations are ordered so that referenced tables exist before foreign keys are created:

1. `users`
2. `password_reset_tokens`
3. `festivals`
4. `festival_settings`
5. `festival_categories`
6. `award_categories`
7. `institutions`
8. `innovations`
9. `festival_juries`
10. `jury_subcategory_participations`
11. `assessment_criteria`
12. `innovation_assessments`
13. `innovation_assessment_details`
14. `institution_assessments`
15. `institution_assessment_details`
16. `innovation_winners`
17. `institution_winners`
18. `audit_logs`

## 2. Installation

1. Back up the target database before making schema changes.
2. Copy the files from `database/migrations/` into your Laravel project's `database/migrations/` directory.
3. **Important:** a fresh Laravel project may already contain a `create_users_table` migration. Replace/adapt that default migration with the supplied `users` migration; do not keep two migrations that both create the `users` table.
4. Check that `.env` points to the intended MySQL database:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_database
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. Review the migration SQL before applying it. On a local development database, run:

   ```bash
   php artisan migrate --pretend
   php artisan migrate
   ```

6. Do not run `php artisan migrate:fresh` on a database containing data you need to keep; it drops tables.

The migrations use Laravel's schema builder and anonymous migration classes. They are intended for a modern Laravel application, but should be executed against the exact Laravel and MySQL/MariaDB versions supported by the hosting provider before production deployment.

## 3. Important design decisions

- `festival_categories` stores the main festival category and its `assessment_type` (`innovation` or `institution`).
- `award_categories` stores subcategories and the per-subcategory `winner_count`.
- Juries choose subcategories through `jury_subcategory_participations`. The UI flow remains: choose festival year → choose main category → choose subcategory.
- Opening an `award_categories` row locks its selected jury roster and participant list. This transition must be enforced in Laravel services/transactions; the migration alone cannot enforce the full workflow.
- Institutions and innovations are stored per festival year. Composite foreign keys help prevent cross-festival and cross-category relationships.
- Innovation and institution assessments use separate header and detail tables.
- Assessment totals and cross-jury averages are calculated from detail rows. They are not stored as duplicate summary columns in this initial schema.
- Winners are selected manually by superadmin. The system stores each winner's position.
- `audit_logs` uses a polymorphic reference (`auditable_type`, `auditable_id`) for flexible audit targets.
- User and participant soft deletes do not physically remove historical rows.

## 4. Application-level validations still required

Some business rules span multiple rows or tables and cannot be safely expressed as a simple portable column constraint. Implement them in Form Requests, Policies, Services, and database transactions:

1. `users.role` must be `superadmin` or `jury`.
2. `festival_categories.assessment_type` must be `innovation` or `institution`.
3. A category's assessment type must match the participant type stored in its subcategory.
4. `assessment_criteria.assessment_type` must be `innovation` or `institution`.
5. Every criterion score must be an integer from 6 through 10.
6. Weights for each festival and assessment type must total exactly 100.
7. A detail row must use a criterion from the same festival and matching assessment type as its assessment. The detail migrations carry `festival_id` and `assessment_type` to support composite foreign keys; set the correct fixed type in the application.
8. A juri may only assess objects in a subcategory for which that juri has a participation row.
9. A juri's participation row can be created only while the subcategory is in `preparation`.
10. When a subcategory changes from `preparation` to `open`, lock the roster and participant set in one database transaction. Do not permit new participants or new jury participation afterward.
11. Before marking a jury participation `completed`, ensure every active object in that subcategory has a complete submitted assessment from that juri.
12. Do not close a subcategory until all required jury assessments are submitted and locked, including any manually handled exception approved by superadmin.
13. Only `submitted` or `locked` complete assessments may be included in final averages; define the exact inclusion rule consistently in the scoring service.
14. Calculate weighted totals and cross-jury averages using sufficient decimal precision, then format displayed values to two decimal places.
15. Superadmin may revise participant identity fields after opening a subcategory, but must not change participant membership, category relations, or institution relations. Record every such change in `audit_logs`.
16. `winner_count` must be a positive integer. Superadmin may appoint only positions within the configured count, and an object must not receive multiple positions in the same subcategory.
17. Results must not be exposed to public routes until `festivals.results_published_at` is set by superadmin.
18. Soft-deleting a user must not delete assessment or audit history.
19. Use a database transaction when submitting assessments, opening/closing a subcategory, revising/locking assessments, appointing winners, or publishing results.
20. For audit entries, use stable morph aliases for `auditable_type` rather than relying on PHP class names that may change during refactoring.

## 5. Database compatibility notes

- The schema targets MySQL with the InnoDB storage engine. Composite foreign keys are used to enforce same-festival relationships. MySQL requires compatible column types and appropriate indexes for referenced composite keys.
- Exact MySQL/MariaDB version on ArenHost Competent has not yet been confirmed. Run the migrations on a local database using the same engine/version family before production.
- The migrations intentionally use application-level validation for score range and total weight rather than database `CHECK` constraints, pending confirmation of the hosting database version and its constraint behavior.
- `unsignedTinyInteger('score')` alone does **not** restrict values to 6–10; validation in Laravel is mandatory.
- The `assessment_type` values, lifecycle status values, winner count, and total-weight rule must be enforced by application validation.
- Parent records use restrictive delete behavior where deleting them could damage historical relationships. Participant rows and user rows include soft deletes where appropriate.

## 6. Recommended next implementation steps

1. Confirm ArenHost's exact MySQL/MariaDB version and Laravel/PHP compatibility.
2. Run these migrations on a clean local database.
3. Create seeders for the initial festival categories and assessment criteria after the first festival is created.
4. Implement Eloquent models and relationships.
5. Implement services for opening subcategories, jury participation, score submission, locking/revision, averages, winner appointment, and publication.
6. Add feature tests for the constraints and workflows listed above.

## 7. Validation performed for this package

- PHP syntax lint was run against all migration files successfully.
- The migrations have **not** been executed against an actual Laravel installation or ArenHost database in this environment. Database-engine integration testing remains required before production use.

<?php

namespace App\Support;

/**
 * Segmentos de ruta reutilizados en routes/web.php (SonarQube S1192).
 */
final class WebRoutePaths
{
    public const GRADES = '/grades';

    public const ATTENDANCE = '/attendance';

    public const PROFILE = '/profile';

    public const CLASSROOMS = '/classrooms';

    public const CLASSROOM = '/classrooms/{classroom}';

    public const MEETINGS = '/meetings';

    public const GRADES_RECORDS = '/grades/records';

    public const ANNOUNCEMENT = '/{announcement}';

    public const INTRANET_SECURITY = 'intranet/security';

    public const INTRANET_ACADEMIC = 'intranet/academic';

    public const DIAGNOSTIC_EXAM = '/diagnostic-exams/{diagnostic_exam}';

    public const EDUCATIONAL_LEVEL = '/levels/{educational_level}';

    public const GRADE = '/grades/{grade}';

    public const SECTION = '/sections/{section}';

    public const SUBJECT = '/subjects/{subject}';

    public const EVALUATION = '/evaluations/{evaluation}';

    public const INTRANET_STUDENT = '/intranet/students/{student}';

    public const INTRANET_GUARDIAN = '/intranet/guardians/{guardian}';

    public const INTRANET_ENROLLMENT = '/intranet/enrollments/{enrollment}';

    public const PAYMENT_CONCEPT = '/intranet/payment-concepts/{payment_concept}';

    public const PENSION = '/intranet/pensions/{pension}';

    public const PRODUCT_CATEGORY = '/intranet/inventory/categories/{product_category}';

    public const PRODUCT = '/intranet/inventory/products/{product}';
}

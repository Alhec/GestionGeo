<?php
/**
 * Created by PhpStorm.
 * User: hector
 * Date: 25/07/20
 * Time: 08:17 PM
 */

namespace App\Exports\AnnualReportSheets;


use App\Services\AnnualReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class EnrolledStudents implements FromArray, WithStrictNullComparison, WithTitle,ShouldAutoSize
{

    protected $schoolPeriods;
    protected $organizationId;

    public function __construct($schoolPeriods,$organizationId)
    {
        $this->schoolPeriods = $schoolPeriods;
        $this->organizationId=$organizationId;
    }
    /**
     * @return array
     */
    public function array(): array
    {
        // TODO: Implement array() method.
        return AnnualReportService::getEnrolledStudents($this->schoolPeriods,$this->organizationId);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // TODO: Implement title() method.
        return 'ESTUDIANTES INSCRITOS';
    }
}

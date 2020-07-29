<?php
/**
 * Created by PhpStorm.
 * User: hector
 * Date: 29/07/20
 * Time: 03:51 PM
 */

namespace App\Exports\AnnualReportSheets;


use App\Services\AnnualReportService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\WithTitle;

class GuestTeachers implements FromArray, WithStrictNullComparison, WithTitle,ShouldAutoSize
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
        return AnnualReportService::getGuestTeachers($this->schoolPeriods,$this->organizationId);
    }

    /**
     * @return string
     */
    public function title(): string
    {
        // TODO: Implement title() method.
        return 'PROFESORES INVITADOS';
    }
}

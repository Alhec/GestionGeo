<?php
/**
 * Created by PhpStorm.
 * User: hector
 * Date: 25/07/20
 * Time: 07:25 PM
 */

namespace App\Exports;


use App\Exports\AnnualReportSheets\ApprovedFinalWorks;
use App\Exports\AnnualReportSheets\EnrolledStudents;
use App\Exports\AnnualReportSheets\GuestTeachers;
use App\Exports\AnnualReportSheets\IrregularFinalWorks;
use App\Exports\AnnualReportSheets\IrregularStudents;
use App\Exports\AnnualReportSheets\NotConduciveToDegree;
use App\Exports\AnnualReportSheets\TeachingGroup;
use function Complex\negative;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;


class AnnualReport implements  WithMultipleSheets
{

    use Exportable;
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
    public function sheets(): array
    {
        // TODO: Implement sheets() method.
        $sheets = [];
        $sheets[0]= new EnrolledStudents($this->schoolPeriods,$this->organizationId);
        $sheets[1]= new IrregularStudents($this->schoolPeriods,$this->organizationId);
        $sheets[2]= new NotConduciveToDegree($this->schoolPeriods,$this->organizationId);
        $sheets[3]= new IrregularFinalWorks($this->schoolPeriods,$this->organizationId);
        $sheets[4]= new ApprovedFinalWorks($this->schoolPeriods,$this->organizationId);
        $sheets[5]= new TeachingGroup($this->schoolPeriods,$this->organizationId);
        $sheets[6]= new GuestTeachers($this->schoolPeriods,$this->organizationId);

        return $sheets;
    }
}

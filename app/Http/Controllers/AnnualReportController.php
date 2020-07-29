<?php
/**
 * Created by PhpStorm.
 * User: hector
 * Date: 25/07/20
 * Time: 06:51 PM
 */
namespace App\Http\Controllers;

use App\Services\AnnualReportService;
use Excel;
use Illuminate\Http\Request;

class AnnualReportController extends Controller
{
    public static function exportAnnualReport(Request $request)
    {
        $organizationId = $request->header('Organization-Key');
        $firstSchoolPeriodId = $request->input('first_school_period_id');
        $lastSchoolPeriodId = $request->input('last_school_period_id');
        return AnnualReportService::exportAnnualReport($firstSchoolPeriodId,$lastSchoolPeriodId,$organizationId);
    }
}

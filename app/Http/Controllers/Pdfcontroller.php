<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;

use PDF;

class PdfDemoController extends Controller
{
    public function index()
    {
        $company = DB::table('companies')->select('company_name')->get();
        $urns = DB::table('registered_students')->select('urn')->get();
        $resp = array('company' => $company, 'urns' => $urns);
        return view('form')->with('resp', $resp);
    }

    public function makePDF(Request $request)
    {
        if ($request->has('modbtn')) {
            DB::update('update registered_students set name = ?, email = ?, branch = ?, phone = ?, company_name = ? where urn = ?', [
                $request->input('name'), $request->input('email'),
                $request->input('branch'), $request->input('tel'), $request->input('company'),
                $request->input('roll')
            ]);
        } else {
            DB::insert('insert into registered_students values( ?, ?, ?, ?, ?, ? )', [
                $request->input('roll'),
                $request->input('name'), $request->input('email'),
                $request->input('branch'), $request->input('tel'), $request->input('company')
            ]);
        }

        //Add page
        PDF::SetTitle('Training Form');
        PDF::AddPage();
        $width = PDF::getPageWidth();
        $height = PDF::getPageHeight();

        //Add header, footer and watermark
        PDF::Image("images\watermark.png", 17.5, 50, $width - 35, $height - 90);
        PDF::Image('images\header.png', 17.5, 10, $width - 35);
        PDF::Image('images\footer.png', 12.5, $height - 40, $width - 25);
        PDF::SetMargins(20, 0, 20);
        //Add Border
        PDF::Rect(10, 10, $width - 20, $height - 20);

        //Writing text
        PDF::SetFont('Helvetica', '', 10);
        PDF::SetY(55);
        PDF::Write(4.5, 'Ref.No. PTP/20');

        PDF::SetXY($width - 55, 55);
        PDF::Write(4.5, 'Dated: ' . date("d/m/Y"));

        PDF::SetY(65);
        PDF::Write(4.5, 'To');

        PDF::SetY(70);
        PDF::Write(4.5, $request->input('company'));

        PDF::SetY(80);
        PDF::SetFont('Helvetica', 'B', 9.6);
        PDF::Write(4.5, "SUBJECT: REQUEST FOR INDUSTRIAL TRAINING OF B.TECH 7th/8th SEMESTER STUDENTS\n\n");

        PDF::SetFont('Helvetica', '', 10);
        PDF::Write(4.5, "Sir,\n\nGreetings from GNDEC, Ludhiana.\n\nGuru Nanak Dev Engineering College has emerged as one of the most prestigious engineering institute of North India over the 62 years of its inception and is conducting B.Tech. in seven disciplines as well as M.Tech.,MBA and PhD. for meeting the research requirement of technical field.");
        PDF::Write(4.5, "\n\nSince practical training is equal in importance to theoretical foundation, the course curriculum is so designed that the students get exposure to practical aspects of their respective engineering branch. We are in a process of enrolling the final year students of our institute to various Industrial Organisations for");
        PDF::SetFont('Helvetica', 'B', 10);
        PDF::Write(4.5, " INDUSTRIAL TRAINING (6 MONTHS) ");
        PDF::SetFont('Helvetica', '', 10);
        PDF::Write(4.5, "which is an essential component of their four year B.Tech programme.\n\n");
        PDF::SetFont('Helvetica', 'U', 10);
        PDF::Write(4.5, "The programme will be as under:\n\n");

        PDF::SetFont('Helvetica', '', 10);


        $html = "
            <ul>
                <li>To get familiar with the setup and working of the organisation. </li>
                <li> Preparation and submission of synopsis.Working on the given project- provided by the company.</li>
                <li>Submission of Mid Term and Final Report.</li>
                <li> Submission of Daily Diary at the end of training maintained & checked by the company representative.</li>
            </ul>";

        PDF::writeHTML($html, true, false, true, false, '');

        PDF::SetFont('Helvetica', 'B', 10);
        PDF::Write(4.5, "\nWe recommend our graduating student Mr./Ms. ");

        PDF::SetFont('HelveticaB', 'U', 10);
        PDF::Write(4.6,  $request->input('name') );

        PDF::SetFont('Helvetica', 'B', 10);
        PDF::Write(4.5,  ", Roll no. ");

        PDF::SetFont('HelveticaB', 'U', 10);
        PDF::Write(4.6,  $request->input('roll') );
        
        PDF::SetFont('Helvetica', 'B', 10);
        PDF::Write(4.5, " of B.Tech (Branch) ");

        PDF::SetFont('HelveticaB', 'U', 10);
        PDF::Write(4.6, $request->input('branch'));

        PDF::SetFont('Helvetica', 'B', 10);
        PDF::Write(4.5, ", Email Id " );

        PDF::SetFont('HelveticaB', 'U', 10);
        PDF::Write(4.6,  $request->input('email') );

        PDF::SetFont('Helvetica', 'B', 10);
        PDF::Write(4.5,  ", Phone no. " );

        PDF::SetFont('HelveticaB', 'U', 10);
        PDF::Write(4.6,  $request->input('tel') );

        PDF::SetFont('Helvetica', 'B', 10);
        PDF::Write(4.5,  " to undergo Industrial training in your esteemed organization starting from March 2020 .\n");
        PDF::SetFont('Helvetica', '', 8);
        PDF::Write(4, "(* Exact date of joining may be intimated at a later stage. An early and favourable response will be highly appreciated.)");

        PDF::SetFont('Helvetica', '', 10);
        PDF::Write(4.5, "\n\nWe would highly appreciate if the student can be accommodated for the training programme. Our students are sincere and hard working and we are sure that they will put in their best efforts during the training program. Looking for the confirmation from your side.\n\nYours Sincerely");
        PDF::Write(4.5, "\n\n\nProf. G.S. Sodhi\nTraining & Placement Officer");

        PDF::Output('Training Form.pdf');
    }
}

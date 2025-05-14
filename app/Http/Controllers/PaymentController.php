<?php

namespace App\Http\Controllers;

use App\Models\Configuration;
use App\Models\Employer;
use App\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use PDF;
class PaymentController extends Controller
{
    public function index(){

        $defaultPaymentDateQuery = Configuration::where(
            'type', 'PAYMENT_DATE', )->first();
        $defaultPaymentDate = $defaultPaymentDateQuery->value;
        $convertedPaymentDate = intval($defaultPaymentDate);
        $today = date('d');

        $isPaymentDay = false;

        if($today == $convertedPaymentDate ){
            $isPaymentDay = true;
        }

        $payments = Payment::latest()->orderBy('id','desc')->paginate(10);
        return view('payments.index', compact('payments', 'isPaymentDay'));
    }

    public function initPayment(){

        //Verifier que nous somme a la date de paiement avant d'executer le code ci dessous
        $monthMapping =[
                'JANUARY'=>'JANVIER',
                'FEBRUARY'=>'FEVRIER',
                'MARCH'=>'MARS',      // was MARS, but needed key 'MARCH'
                'APRIL'=>'AVRIL',
                'MAY'=>'MAI',          // ✅ this is the fix
                'JUNE'=> 'JUIN',
                'JULY'=>'JUILLET',
                'AUGUST' => 'AOÛT',
                'SEPTEMBER'=>'SEPTEMBRE',
                'OCTOBER'=>'OCTOBRE',
                'NOVEMBER'=> 'NOVEMBRE',
                'DECEMBER'=>'DECEMBRE'
            ];
        $currentMonth = strtoupper(Carbon::now()->format('F')); // fixed method call

        // mois en cours en français
        $currentMonthInFrench = $monthMapping[$currentMonth] ?? '';

        //année en cours
        $currentYear = Carbon::now()->format('Y');

        //simuler tous les paiements pour tous les employers dans le mois en cours.
        //les paiements concerne les employers qui n'ont pas encore été payés
        //dans le mois actuel.

        //Recuperer la liste des emploeyrs qui n'ont pas été payé pour le mois en cour.
        $employers = Employer::whereDoesntHave('payments', function
        ($query) use($currentMonthInFrench, $currentYear) {
            $query->where('month', '=', $currentMonthInFrench)
                ->where('year', '=', $currentYear);
        })->get();

        if( $employers->count() == 0 ){
            return redirect()->back()->with(
                'error_message', 'Tous vos employés ont été payés pour ce mois '.$currentMonthInFrench);
        }
        //Faire les paiements pour ces employers
        foreach($employers as $employer){
            $aEtePayer = $employer->payments()->where('month', '=', $currentMonthInFrench)->where('year', '=', $currentYear)->exists();

            if(!$aEtePayer){
                $salaire = $employer->montant_journalier * 31; // fixed: $employers -> $employer

                $payment = new Payment([
                    'reference' => strtoupper(Str::random(10)), // fixed: str::random -> Str::random
                    'employer_id'=>$employer->id,
                    'amount'=>$salaire,
                    'launch_date'=>now(),
                    'done_date'=>now(),
                    'status'=>'SUCCESS',
                    'month'=>$currentMonthInFrench,
                    'year'=>$currentYear
                ]);

                $payment->save();
            }
        }

        //Rediriger
        return redirect()->back()->with('success_message', 'Paiement des employers effectués pour le mois de '. $currentMonthInFrench ); // moved return inside function
    }



    public function downloadInvoice(Payment $payment){
        try{
            $fullPaymentInfo = Payment::with('employer')->find($payment->id);

            //Generer le pdf
            // return view('payments.facture', compact('fullPaymentInfo'));

            $pdf = PDF::loadView('payments.facture', compact('fullPaymentInfo'));
            return $pdf->download('facture_' . $fullPaymentInfo->employer->nom . '.pdf');

        }catch(Exception $e){
            throw new Exception("Une arreur est survenue lors du téléchargement de la facture ");
        }
    }
}

<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Ticket;
use App\TicketNumber;
use Validator;

class TicketController extends Controller
{
	/**
	 * Generates a unique ticket
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
    public function submit(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'number' => 'required',
            'name' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
            	'success' => false,
            	'message' => $validator->errors()->first(),
            ], 422);
        }

        $check = $this->alreadyExists($request->number, true);

        if ($check['exists']) {
        	return response()->json([
            	'success' => false,
            	'message' => "Sorry, #{$request->number} is already taken on "
                    .$check['ticket']->created_at->format('M d, Y H:i:s')
            ]);
        }

    	$ticket = new Ticket();
    	$ticket->number = $request->number;
    	$ticket->name = $request->name;


        $numbers = explode('-', $request->number);

        if (!empty($this->isNotUnique($numbers))) {
            return response()->json([
                'success' => false,
                'message' => "All 3 numbers must be unique."
            ], 422);
        }

        foreach ($numbers as $key => $number) {
            if (empty($number) || $number == '00' || $number == 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Please provide all 3 numbers. Note: 00 is not accepted.",
                ], 422);
            }
        }

        $ticket->save();

        

        // save ticket numbers
        foreach ($numbers as $key => $number) {
            TicketNumber::create([
                'ticket_id' => $ticket->id,
                'number' => $number
            ]);
        }

    	if (!empty($ticket->numbers)) {
    		return response()->json([
    			'success' => true,
    			'message' => "Your ticket with #{$request->number} have been submitted successfully."
    		]);
    	}

    	return response()->json([
			'success' => false,
			'message' => "Something went wrong while trying to submit your number. Please try again later."
		], 500);
    }

    /**
     * Determine if a given array has duplicates
     *
     * @param  array $raw_array
     * @return bool
     */
    public function isNotUnique($raw_array) {
        $dupes = array();
        natcasesort($raw_array);
        reset($raw_array);

        $old_key   = NULL;
        $old_value = NULL;
        foreach ($raw_array as $key => $value) {
            if ($value === NULL) { continue; }
            if (strcasecmp($old_value, $value) === 0) {
                $dupes[$old_key] = $old_value;
                $dupes[$key]     = $value;
            }
            $old_value = $value;
            $old_key   = $key;
        }
        return $dupes;
    }

    /**
     * Generates a 3 random numbers
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function createRandomNumbers(Request $request)
    {
    	$digits = 3;

    	$numbers = [];

    	for ($i=0; $i < $digits; $i++) { 
    		$numbers[] = $this->generate(2);
    	}

    	return response()->json([
    		'success' => true,
    		'numbers' => $numbers
    	]);
    }

    /**
     * Generates a random number
     *
     * @param  int $digits The number of digits to generate
     * @return int
     */
    public function generate($digits)
    {

    	$number = str_pad(rand(1, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);

    	if ($this->alreadyExists($number)) {
    		$number = $this->generate($digits);
    	}

    	return $number;
    }

    /**
     * Checks for existing ticket number
     *
     * @param  int $number
     * @param  bool $returnsTicket Whether to return the object or not
     * @return bool|array
     */
    public function alreadyExists($number, $returnsTicket = false)
    {
        $numbers = explode('-', $number);

        $combinations = $this->getCombinations($numbers);

    	// check first for existing number
    	$ticket = Ticket::whereIn("number", $combinations)->first();

    	if (!$returnsTicket) {
    		return !empty($ticket);
    	}

    	return [
    		'exists' => !empty($ticket),
    		'ticket' => $ticket
    	];
    }

    /**
     * Returns all possible combinations of items in an array
     *
     * @param  array $words
     * @return array
     */
    public function getCombinations($words) {
        if ( count($words) <= 1 ) {
            $result = $words;
        } else {
            $result = array();
            for ( $i = 0; $i < count($words); ++$i ) {
                $firstword = $words[$i];
                $remainingwords = array();
                for ( $j = 0; $j < count($words); ++$j ) {
                    if ( $i <> $j ) $remainingwords[] = $words[$j];
                }
                $combos = $this->getCombinations($remainingwords);
                for ( $j = 0; $j < count($combos); ++$j ) {
                    $result[] = $firstword . '-' . $combos[$j];
                }
            }
        }
        return $result;
    }


    /**
     * Checks for existing ticket
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function checkForDuplicates(Request $request)
    {
    	$validator = Validator::make($request->all(), [
            'number' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
            	'success' => false,
            	'message' => $validator->errors()->first(),
            ], 422);
        }

        $number = $request->number;
        $check = $this->alreadyExists($number, true);

        if ($check['exists']) {
        	return response()->json([
            	'success' => false,
            	'message' => "Sorry, #$number is aready taken on ".$check['ticket']
                    ->created_at->format('M d, Y H:i:s')
            ]);
        }

        return response()->json([
        	'success' => true,
        	'message' => "$number is available."
        ]);
    }
}

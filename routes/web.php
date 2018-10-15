<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get("try", function () {
	 $array = array('20', '21', '01');

    function wordcombos ($words) {
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
                $combos = wordcombos($remainingwords);
                for ( $j = 0; $j < count($combos); ++$j ) {
                    $result[] = $firstword . '-' . $combos[$j];
                }
            }
        }
        return $result;
    }

    dump(wordcombos($array));
});
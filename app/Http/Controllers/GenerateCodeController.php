<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;

class GenerateCodeController extends Controller
{
    private $digits_length = 4;
    private $pin_array = [];
    private $min_number = 0;
    private $max_number = 9;
    private $possibile_codes = [];

    public function index()
    {
        return view('welcome');
    }

    public function get_code(Request $request)
    {
        $this->set_all_possible_codes();

        $possible_code = $this->possibile_codes[ mt_rand(0, count($this->possibile_codes)-1) ];
        if ( count($possible_code) > 0) {
            $code = null;
            for ($i = 0; $i < $this->digits_length ; $i++ ) {
                $code .= $possible_code[$i];
            }
            return response()->json([
                'type' => 'success',
                'code' => view('code', compact('code'))->render() ,
            ]);
        }

        return response()->json([
            'type' => 'error',
            'message' => 'No more available codes',
        ]);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function set_all_possible_codes()
    {
        for( $i = 0; $i < $this->digits_length; $i++ ) {
            $this->pin_array[$i] = $this->min_number;
        }
        for ($x = 0; $x <= 10 ** $this->digits_length; $x++){
            $this->generate_code();
        }
    }

    public function generate_code()
    {
        for ($j = $this->digits_length-1 ; $j >= 0 ; $j--) {
            
            if( $this->pin_array[$j] < $this->max_number ) {
                $this->pin_array[$j]++;
                break;
            } else {
                if($j > 0 && $this->pin_array[$j-1] < $this->max_number ) {
                    $this->pin_array[$j-1]++;
                    for($q = $j ; $q < $this->digits_length; $q++){
                        $this->pin_array[$q] = $this->min_number;
                    } 
                    break;
                } else {
                    continue;       
                }
            }
        }
        if ( $this->is_not_sequential_ordered() && $this->is_not_obvious_combinations()) {
            $this->possibile_codes[] = $this->pin_array;
        }
        return NULL;
    }

    private function is_not_sequential_ordered()
    {
        for ($j = $this->digits_length -1 ; $j >= 0 ; $j--) {
            if ($j > 0 && ($this->pin_array[$j] - $this->pin_array[$j-1] != 1) ) {
                return true;
            }
        }
        return false;
    }

    private function is_not_obvious_combinations()
    {
        for ($j = $this->digits_length -1 ; $j >= 0 ; $j--) {
            if ($j > 0 && $this->pin_array[$j] != $this->pin_array[$j-1] ) {
                return true;
            }
        }
        return false;
    }

}

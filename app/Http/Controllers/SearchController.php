<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(string $searchKey)
    {
        $users = User::all();
        $searchResult = [];

        foreach ($users as $user) {
            if (str_contains(strtoupper($user->name), strtoupper($searchKey)) || str_contains(strtoupper($user->username), strtoupper($searchKey))) {
                $searchResult = [...$searchResult, $user];
            }
        }

        return response()->json([
            'searchResult' => $searchResult
        ]);
    }
}

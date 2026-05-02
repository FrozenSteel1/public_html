<?php

namespace App\Http\Controllers;

use App\Models\GameHistory;
use App\Http\Requests\StoreGameHistoryRequest;
use App\Http\Requests\UpdateGameHistoryRequest;

class GameHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreGameHistoryRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(GameHistory $gameHistory)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GameHistory $gameHistory)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateGameHistoryRequest $request, GameHistory $gameHistory)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(GameHistory $gameHistory)
    {
        //
    }
}

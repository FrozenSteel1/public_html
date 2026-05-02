<?php

namespace App\Http\Controllers;

use App\Models\Preset;
use App\Http\Requests\StorePresetRequest;
use App\Http\Requests\UpdatePresetRequest;

class PresetController extends Controller
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
    public function store(StorePresetRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Preset $preset)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Preset $preset)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePresetRequest $request, Preset $preset)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Preset $preset)
    {
        //
    }
}

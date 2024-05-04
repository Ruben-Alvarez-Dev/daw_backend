<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get("/tables", function () {
    return 'Table list'
});
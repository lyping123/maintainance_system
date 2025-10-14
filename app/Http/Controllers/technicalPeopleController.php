<?php

namespace App\Http\Controllers;

use App\Models\technical_person;
use Illuminate\Http\Request;
use Inertia\Inertia;

use function Termwind\render;

class technicalPeopleController extends Controller
{
    public function index()
    {
        $technicalPeople = technical_person::all();
        return Inertia::render("TechnicalPerson",compact('technicalPeople'));
    }
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' =>'required|max:255',
            'email' =>'required|email|unique:technical_people',
            'phone' =>'required|max:255',
            'field'=> 'required|max:255',
            'address' =>'required|max:255',
        ]);
        $technical_person = technical_person::create($validatedData);
        if($technical_person){
            return redirect()->route('technical.index')->with('success', 'Technical Person Created Successfully.');
        }
        return back()->withInput()->with('errors', 'Technical Person Created Failed.');
    }

    public function update(Request $request, technical_person $technical_person)
    {
        $validatedData = $request->validate([
            'name' =>'required|max:255',
            'email' =>'required|email|unique:technical_people,email,'.$technical_person->id,
            'phone' =>'required|max:255',
            'field'=> 'required|max:255',
            'address' =>'required|max:255',
        ]);
        $technical_person->update($validatedData);
        return redirect()->route('technical.index')->with('success', 'Technical Person Updated Successfully.');
    }
    

    public function destroy(technical_person $technical_person)
    {
        $technical_person->delete();
        return redirect()->route('technical.index')->with('success', 'Technical Person Deleted Successfully.');
    }



}

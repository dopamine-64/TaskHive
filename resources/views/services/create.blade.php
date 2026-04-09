@extends('layouts.app')
@section('title', 'Post Service')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), 
                    url('/images/bg-1.png') no-repeat center center !important;
        background-size: cover !important;
        background-attachment: fixed !important;
    }

    .elegant-light-card {
        background-color: #f0f4f3 !important; 
        border: 1px solid #e1e8e5;
        border-radius: 16px !important;
        font-family: 'Poppins', sans-serif; 
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3) !important;
    }

    .elegant-light-card h2 {
        font-family: 'Poppins', sans-serif;
        font-weight: 700;
        color: #005c4b;
        letter-spacing: -0.5px;
    }

    .elegant-input {
        background-color: #ffffff !important; 
        border: 1px solid #cdd6d2 !important;
        border-radius: 10px !important;
        padding: 0.75rem 1rem;
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
        color: #2c3e38 !important;
        transition: all 0.3s ease;
    }

    .elegant-input:focus {
        border-color: #005c4b !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 92, 75, 0.15) !important;
        outline: none;
    }

    .elegant-label {
        font-weight: 500;
        color: #3b5249;
        font-size: 0.9rem;
        margin-bottom: 0.4rem;
    }

    .elegant-btn {
        background-color: #005c4b;
        color: white;
        border-radius: 20px;
        font-weight: 600;
        font-family: 'Poppins', sans-serif;
        border: none;
        transition: all 0.3s ease;
    }

    .elegant-btn:hover {
        background-color: #004538;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 92, 75, 0.3);
    }
</style>
@endsection

@section('content')
<div class="container py-3" style="z-index: 10;">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg elegant-light-card">
                <div class="card-body p-3">
                    <h2 class="mb-4 text-center">Post a New Service</h2>
                    
                    <form action="{{ route('services.store') }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="form-label elegant-label">Service Title</label>
                            <input type="text" name="title" class="form-control elegant-input" required placeholder="e.g. Professional Plumbing Services">
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label class="form-label elegant-label">Category</label>
                                <select name="category" class="form-select elegant-input" required>
                                    <option value="" disabled selected>Select Category</option>
                                    <option value="Home Maintenance">Home Maintenance</option>
                                    <option value="Web Development">Web Development</option>
                                    <option value="Design">Design</option>
                                    <option value="Tutoring">Tutoring</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label elegant-label">Subcategory (Optional)</label>
                                <input type="text" name="subcategory" class="form-control elegant-input" placeholder="e.g. Pipe Fixing">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label elegant-label">Price (৳)</label>
                            <input type="number" step="1" name="price" class="form-control elegant-input" required placeholder="0">
                        </div>

                        <div class="mb-5">
                            <label class="form-label elegant-label">Description</label>
                            <textarea name="description" class="form-control elegant-input" rows="4" required placeholder="Describe what you offer..."></textarea>
                        </div>

                        <button type="submit" class="btn w-100 py-3 elegant-btn">Publish Service</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
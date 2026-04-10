{{-- Edit Provider Profile --}}
@extends('layouts.app')

@section('title', 'Edit Provider Profile')

@section('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

    body {
        background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.7)), 
                    url('/images/bg-1.png') no-repeat center center !important;
        background-size: cover !important;
        background-attachment: fixed !important;
    }

    .profile-edit-container {
        background-color: #f0f4f3 !important;
        border: 1px solid #e1e8e5;
        border-radius: 16px !important;
        padding: 2rem;
        margin: 2rem 0;
        font-family: 'Poppins', sans-serif;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3) !important;
    }

    .profile-edit-container h1 {
        color: #005c4b !important;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .form-section {
        margin-bottom: 2rem;
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 12px;
        border: 1px solid #e1e8e5;
    }

    .form-section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        color: #005c4b;
        font-family: 'Poppins', sans-serif;
    }

    .form-group label {
        color: #3b5249;
        margin-bottom: 0.5rem;
        font-weight: 500;
        font-size: 0.9rem;
        font-family: 'Poppins', sans-serif;
    }

    .form-control, .form-select {
        background-color: #ffffff !important;
        border: 1px solid #cdd6d2 !important;
        color: #2c3e38 !important;
        border-radius: 10px !important;
        padding: 0.75rem 1rem;
        font-family: 'Poppins', sans-serif;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #005c4b !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 92, 75, 0.15) !important;
        outline: none;
        background-color: #ffffff !important;
        color: #2c3e38 !important;
    }

    .form-control::placeholder {
        color: #999;
    }

    .form-text {
        color: #3b5249;
        font-size: 0.85rem;
        font-family: 'Poppins', sans-serif;
    }

    .skills-input-container {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .skill-input {
        flex: 1;
    }

    .skill-tag {
        display: inline-block;
        background: rgba(0, 92, 75, 0.15);
        border: 1px solid rgba(0, 92, 75, 0.3);
        border-radius: 20px;
        padding: 0.5rem 1rem;
        color: #005c4b;
        margin: 0.5rem 0.5rem 0.5rem 0;
        font-family: 'Poppins', sans-serif;
        font-size: 0.9rem;
    }

    .skill-tag .remove {
        cursor: pointer;
        margin-left: 0.5rem;
        font-weight: bold;
    }

    .btn-primary {
        background: #005c4b !important;
        border: none !important;
        color: white !important;
        font-weight: 600;
        border-radius: 20px !important;
        font-family: 'Poppins', sans-serif;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background: #004538 !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 92, 75, 0.3) !important;
        color: white !important;
    }

    .btn-secondary {
        background: #f0f4f3 !important;
        border: 1px solid #cdd6d2 !important;
        color: #005c4b !important;
        border-radius: 20px !important;
        font-family: 'Poppins', sans-serif;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: #e8eceb !important;
        border-color: #005c4b !important;
        transform: translateY(-2px);
    }

    .error-messages {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        font-family: 'Poppins', sans-serif;
    }

    .error-messages ul {
        margin: 0;
        padding-left: 1.5rem;
        color: #dc3545;
    }

    .error-messages li {
        margin: 0.5rem 0;
    }

    .button-group {
        display: flex;
        gap: 1rem;
        margin-top: 2rem;
        flex-wrap: wrap;
    }

    .button-group .btn {
        flex: 1;
        min-width: 150px;
    }
</style>
@endsection

@section('content')
<div class="container py-5" style="z-index: 10;">
    <div class="profile-edit-container" style="max-width: 800px; margin: 0 auto;">
        <h1 style="margin-bottom: 2rem;">Edit Provider Profile</h1>

        @if($errors->any())
            <div class="error-messages">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Bio Section -->
            <div class="form-section">
                <h3 class="form-section-title">Professional Information</h3>
                
                <div class="form-group mb-3">
                    <label for="bio" class="form-label">Professional Bio</label>
                    <textarea 
                        name="bio" 
                        id="bio" 
                        class="form-control @error('bio') is-invalid @enderror" 
                        rows="4"
                        placeholder="Tell us about yourself, your background, and expertise..."
                    >{{ old('bio', $profile->bio) }}</textarea>
                    @error('bio')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text">Maximum 1000 characters</small>
                </div>
            </div>

            <!-- Experience & Skills Section -->
            <div class="form-section">
                <h3 class="form-section-title">Experience & Skills</h3>
                
                <div class="form-group mb-3">
                    <label for="experience_years" class="form-label">Years of Experience</label>
                    <input 
                        type="number" 
                        name="experience_years" 
                        id="experience_years" 
                        class="form-control @error('experience_years') is-invalid @enderror"
                        min="0"
                        value="{{ old('experience_years', $profile->experience_years) }}"
                        placeholder="e.g., 5"
                    >
                    @error('experience_years')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group mb-3">
                    <label class="form-label">Skills</label>
                    <div class="skills-input-container">
                        <input 
                            type="text" 
                            id="skill-input" 
                            class="form-control skill-input"
                            placeholder="Type a skill and press Enter"
                        >
                        <button type="button" class="btn btn-primary" id="add-skill-btn">Add</button>
                    </div>
                    <input type="hidden" name="skills" id="skills-hidden" value='{{ json_encode($profile->skills ?? []) }}'>
                    <div id="skills-container">
                        @foreach($profile->skills ?? [] as $skill)
                            <span class="skill-tag" data-skill="{{ $skill }}">
                                {{ $skill }}
                                <span class="remove" onclick="removeSkill(this)">×</span>
                            </span>
                        @endforeach
                    </div>
                    <small class="form-text d-block mt-2">Add multiple skills to showcase your expertise</small>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="form-section">
                <h3 class="form-section-title">Pricing</h3>
                
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label for="hourly_rate" class="form-label">Hourly Rate ($)</label>
                        <input 
                            type="number" 
                            name="hourly_rate" 
                            id="hourly_rate" 
                            class="form-control @error('hourly_rate') is-invalid @enderror"
                            step="0.01"
                            min="0"
                            value="{{ old('hourly_rate', $profile->hourly_rate) }}"
                            placeholder="e.g., 50.00"
                        >
                        @error('hourly_rate')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 form-group mb-3">
                        <label for="fixed_rate" class="form-label">Fixed Rate ($)</label>
                        <input 
                            type="number" 
                            name="fixed_rate" 
                            id="fixed_rate" 
                            class="form-control @error('fixed_rate') is-invalid @enderror"
                            step="0.01"
                            min="0"
                            value="{{ old('fixed_rate', $profile->fixed_rate) }}"
                            placeholder="e.g., 500.00"
                        >
                        @error('fixed_rate')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Service Area Section -->
            <div class="form-section">
                <h3 class="form-section-title">Service Area</h3>
                
                <div class="row">
                    <div class="col-md-8 form-group mb-3">
                        <label for="service_area" class="form-label">Service Area (City/Region)</label>
                        <input 
                            type="text" 
                            name="service_area" 
                            id="service_area" 
                            class="form-control @error('service_area') is-invalid @enderror"
                            value="{{ old('service_area', $profile->service_area) }}"
                            placeholder="e.g., New York City, California"
                        >
                        @error('service_area')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 form-group mb-3">
                        <label for="service_radius_km" class="form-label">Service Radius (km)</label>
                        <input 
                            type="number" 
                            name="service_radius_km" 
                            id="service_radius_km" 
                            class="form-control @error('service_radius_km') is-invalid @enderror"
                            step="0.1"
                            min="0"
                            value="{{ old('service_radius_km', $profile->service_radius_km) }}"
                            placeholder="e.g., 25"
                        >
                        @error('service_radius_km')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Certifications Section -->
            <div class="form-section">
                <h3 class="form-section-title">Certifications & Credentials</h3>
                
                <div class="form-group mb-3">
                    <label for="certifications" class="form-label">Certifications</label>
                    <textarea 
                        name="certifications" 
                        id="certifications" 
                        class="form-control @error('certifications') is-invalid @enderror"
                        rows="3"
                        placeholder="List your certifications, licenses, or credentials..."
                    >{{ old('certifications', $profile->certifications) }}</textarea>
                    @error('certifications')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    <small class="form-text">e.g., AWS Certified Solutions Architect, PMP License #12345</small>
                </div>
            </div>

            <!-- Button Group -->
            <div class="button-group">
                <button type="submit" class="btn btn-primary btn-lg">Save Profile</button>
                <a href="{{ route('provider.show', auth()->id()) }}" class="btn btn-secondary btn-lg">View Profile</a>
                <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg">Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>

<script>
    const skillsContainer = document.getElementById('skills-container');
    const skillsHidden = document.getElementById('skills-hidden');
    const skillInput = document.getElementById('skill-input');
    const addSkillBtn = document.getElementById('add-skill-btn');

    function getSkills() {
        return Array.from(document.querySelectorAll('.skill-tag')).map(el => el.dataset.skill);
    }

    function updateHiddenInput() {
        skillsHidden.value = JSON.stringify(getSkills());
    }

    function removeSkill(el) {
        el.closest('.skill-tag').remove();
        updateHiddenInput();
    }

    function addSkill() {
        const skill = skillInput.value.trim();
        if (!skill) return;
        
        if (getSkills().includes(skill)) {
            alert('Skill already added');
            return;
        }

        const tag = document.createElement('span');
        tag.className = 'skill-tag';
        tag.dataset.skill = skill;
        tag.innerHTML = `${skill} <span class="remove" onclick="removeSkill(this)">×</span>`;
        skillsContainer.appendChild(tag);
        
        skillInput.value = '';
        updateHiddenInput();
    }

    addSkillBtn.addEventListener('click', addSkill);
    skillInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addSkill();
        }
    });
</script>
@endsection

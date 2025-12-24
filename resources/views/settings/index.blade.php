@extends('layouts.app')

@php
use App\Models\Setting;
@endphp

@section('title', 'Settings')
@section('page-title', 'System Settings')

@section('content')
<div class="max-w-5xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">System Settings</h2>
                <p class="text-sm text-gray-600 mt-1">Configure school information and system preferences</p>
            </div>
        </div>

        @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 rounded-lg">
            <p class="text-green-800 font-semibold">{{ session('success') }}</p>
        </div>
        @endif

        <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- School Information -->
            <div class="mb-8 border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    School Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="school_name" class="block text-sm font-medium text-gray-700 mb-2">School Name *</label>
                        <input 
                            type="text" 
                            id="school_name" 
                            name="settings[school_name]" 
                            value="{{ Setting::get('school_name', 'Global College') }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <div>
                        <label for="school_logo" class="block text-sm font-medium text-gray-700 mb-2">Normal Logo</label>
                        <div class="flex items-center space-x-4">
                            @if(Setting::get('school_logo'))
                            <img src="{{ asset('storage/' . Setting::get('school_logo')) }}" alt="School Logo" class="h-16 w-16 object-contain border border-gray-300 rounded">
                            @else
                            <div class="h-16 w-16 bg-gray-100 border border-gray-300 rounded flex items-center justify-center">
                                <span class="text-gray-400 text-xs">No Logo</span>
                            </div>
                            @endif
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    id="school_logo" 
                                    name="logo" 
                                    accept="image/*"
                                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                <p class="text-xs text-gray-500 mt-1">Used in portal, dashboard, etc. Recommended: 200x200px, PNG or JPG (max 2MB)</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="receipt_logo" class="block text-sm font-medium text-gray-700 mb-2">Receipt Logo</label>
                        <div class="flex items-center space-x-4">
                            @if(Setting::get('receipt_logo'))
                            <img src="{{ asset('storage/' . Setting::get('receipt_logo')) }}" alt="Receipt Logo" class="h-16 w-16 object-contain border border-gray-300 rounded">
                            @else
                            <div class="h-16 w-16 bg-gray-100 border border-gray-300 rounded flex items-center justify-center">
                                <span class="text-gray-400 text-xs">No Logo</span>
                            </div>
                            @endif
                            <div class="flex-1">
                                <input 
                                    type="file" 
                                    id="receipt_logo" 
                                    name="receipt_logo" 
                                    accept="image/*"
                                    class="w-full px-4 py-2 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                >
                                <p class="text-xs text-gray-500 mt-1">Used on receipts only. Recommended: 200x200px, PNG or JPG (max 2MB)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="mb-8 border-b border-gray-200 pb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    Contact Information
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="school_email" class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
                        <input 
                            type="email" 
                            id="school_email" 
                            name="settings[school_email]" 
                            value="{{ Setting::get('school_email', 'info@globalcollege.edu') }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <div>
                        <label for="school_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input 
                            type="text" 
                            id="school_phone" 
                            name="settings[school_phone]" 
                            value="{{ Setting::get('school_phone', '+254 700 000 000') }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label for="school_address" class="block text-sm font-medium text-gray-700 mb-2">Address *</label>
                        <input 
                            type="text" 
                            id="school_address" 
                            name="settings[school_address]" 
                            value="{{ Setting::get('school_address', 'P.O. Box 12345, Nairobi, Kenya') }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>

                    <div class="md:col-span-2">
                        <label for="school_website" class="block text-sm font-medium text-gray-700 mb-2">Website URL</label>
                        <input 
                            type="url" 
                            id="school_website" 
                            name="settings[school_website]" 
                            value="{{ Setting::get('school_website') }}"
                            placeholder="https://www.example.com"
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                </div>
            </div>

            <!-- General Settings -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    General Settings
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="currency" class="block text-sm font-medium text-gray-700 mb-2">Currency Code *</label>
                        <input 
                            type="text" 
                            id="currency" 
                            name="settings[currency]" 
                            value="{{ Setting::get('currency', 'KES') }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        <p class="text-xs text-gray-500 mt-1">e.g., KES, USD, EUR</p>
                    </div>

                    <div>
                        <label for="currency_symbol" class="block text-sm font-medium text-gray-700 mb-2">Currency Symbol *</label>
                        <input 
                            type="text" 
                            id="currency_symbol" 
                            name="settings[currency_symbol]" 
                            value="{{ Setting::get('currency_symbol', 'KES') }}"
                            required
                            class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                        <p class="text-xs text-gray-500 mt-1">Display symbol (e.g., KES, $, €)</p>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 border-2 border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-semibold">
                    Cancel
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg font-bold hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg hover:shadow-xl"
                >
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold text-gray-800">
            Assessment Details
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-8">
                <h3 class="text-2xl font-bold mb-2">{{ $assessment->title }}</h3>
                <div class="flex flex-wrap gap-6 mb-4 text-sm text-gray-600">
                    <div>Course: <span class="font-semibold text-gray-900">{{ $assessment->course->title }}</span></div>
                    <div>Due Date: <span class="font-semibold text-gray-900">{{ $assessment->formatted_due_date }}</span></div>
                    <div>Total Marks: <span class="font-semibold text-gray-900">{{ $assessment->total_marks }}</span></div>
                </div>
                <div class="mb-6">
                    <div class="text-gray-700 mb-2">{{ $assessment->description }}</div>
                </div>
                @php
                    $submission = $existingSubmission ?? ($assessment->submissions->where('student_id', Auth::id())->first() ?? null);
                @endphp
                @if($submission)
                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 mb-4">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                            <div>
                                <div class="font-semibold text-gray-800 mb-1">My Submission</div>
                                <div class="text-sm text-gray-600 mb-1">Submitted: {{ $submission->formatted_submitted_date }}</div>
                                <div class="text-sm text-gray-600 mb-1">Status: <span class="capitalize">{{ $submission->status }}</span></div>
                                @if($submission->marks_obtained !== null)
                                    <div class="text-green-600 font-bold text-lg mb-1">Grade: {{ $submission->marks_obtained }}/{{ $assessment->total_marks }} ({{ $submission->percentage }}%)</div>
                                    @if($submission->feedback)
                                        <div class="text-gray-700 mt-1">Feedback: {{ $submission->feedback }}</div>
                                    @endif
                                @else
                                    <div class="text-yellow-600 font-semibold">Pending Grading</div>
                                @endif
                            </div>
                            <div class="mt-4 md:mt-0 md:ml-6">
                                <a href="{{ route('student.assessments.view-submission', $submission) }}" class="inline-block bg-blue-600 text-white px-4 py-2 rounded font-semibold hover:bg-blue-700">View Submission</a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-4">
                        <div class="text-yellow-800 font-semibold mb-2">You have not submitted this assessment yet.</div>
                        
                        <!-- Submission Form -->
                        <form action="{{ route('student.assessments.submit', $assessment) }}" method="POST" enctype="multipart/form-data" class="mt-4" id="assessmentForm">
                            @csrf
                            <div class="mb-3">
                                <label for="submission_content" class="block text-sm font-medium text-gray-700 mb-1">Your Answer</label>
                                <textarea name="submission_content" id="submission_content" rows="4" required 
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                          placeholder="Write your answer here..."></textarea>
                                @error('submission_content')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="attachment" class="block text-sm font-medium text-gray-700 mb-1">Attachment (Optional)</label>
                                <input type="file" name="attachment" id="attachment" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                       accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png">
                                <p class="text-xs text-gray-500 mt-1">Supported formats: PDF, DOC, DOCX, TXT, JPG, PNG (Max 100MB)</p>
                                @error('attachment')
                                    <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                            @error('submission')
                                <p class="text-red-600 text-sm mb-3">{{ $message }}</p>
                            @enderror
                            
                            <!-- Debug Info -->
                            <div class="bg-gray-100 p-3 rounded mb-3 text-xs">
                                <p><strong>Debug Info:</strong></p>
                                <p>Assessment ID: {{ $assessment->id }}</p>
                                <p>Assessment Status: {{ $assessment->status }}</p>
                                <p>Route: {{ route('student.assessments.submit', $assessment) }}</p>
                                <p>Student ID: {{ Auth::id() }}</p>
                                <p>Course ID: {{ $assessment->course_id }}</p>
                                <p>Form Action: <span id="formAction"></span></p>
                                <p>Form Method: <span id="formMethod"></span></p>
                            </div>
                            
                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700" id="submitBtn">Submit Assessment</button>
                            
                            <!-- Test Button -->
                            <button type="button" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 ml-2" id="testBtn">Test Submission</button>
                            
                            <!-- Manual Submit Button -->
                            <button type="button" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 ml-2" id="manualSubmitBtn">Manual Submit</button>
                            
                            <!-- Simple Submit Button -->
                            <button type="button" class="bg-yellow-600 text-white px-4 py-2 rounded hover:bg-yellow-700 ml-2" id="simpleSubmitBtn">Simple Submit</button>
                        </form>

                        <script>
                        // Debug form information
                        document.addEventListener('DOMContentLoaded', function() {
                            const form = document.getElementById('assessmentForm');
                            const formAction = document.getElementById('formAction');
                            const formMethod = document.getElementById('formMethod');
                            
                            if (form) {
                                formAction.textContent = form.action;
                                formMethod.textContent = form.method;
                                console.log('Form found:', form);
                                console.log('Form action:', form.action);
                                console.log('Form method:', form.method);
                                
                                // Test form submission
                                console.log('Testing form submission...');
                                const testEvent = new Event('submit', { bubbles: true, cancelable: true });
                                form.dispatchEvent(testEvent);
                            } else {
                                console.error('Form not found!');
                            }
                        });
                        
                        document.getElementById('assessmentForm').addEventListener('submit', function(e) {
                            console.log('Form submission started');
                            console.log('Event type:', e.type);
                            console.log('Event target:', e.target);
                            console.log('Event currentTarget:', e.currentTarget);
                            
                            const submitBtn = document.getElementById('submitBtn');
                            const content = document.getElementById('submission_content').value;
                            
                            if (!content.trim()) {
                                e.preventDefault();
                                alert('Please enter your answer before submitting.');
                                return;
                            }
                            
                            submitBtn.disabled = true;
                            submitBtn.textContent = 'Submitting...';
                            console.log('Form data:', {
                                content: content,
                                hasFile: document.getElementById('attachment').files.length > 0,
                                action: this.action,
                                method: this.method
                            });
                            
                            // Add a small delay to see if the form actually submits
                            setTimeout(() => {
                                if (submitBtn.disabled) {
                                    console.log('Form submission may have failed - button still disabled');
                                }
                            }, 2000);
                        });
                        
                        // Test button functionality
                        document.getElementById('testBtn').addEventListener('click', function() {
                            const content = document.getElementById('submission_content').value;
                            const formData = new FormData();
                            formData.append('submission_content', content);
                            formData.append('_token', '{{ csrf_token() }}');
                            
                            console.log('Testing submission to test route...');
                            
                            fetch('{{ route("test.assessment.submission") }}', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Test submission response:', data);
                                alert('Test submission successful! Check console for details.');
                            })
                            .catch(error => {
                                console.error('Test submission error:', error);
                                alert('Test submission failed! Check console for details.');
                            });
                        });
                        
                        // Manual submit button
                        document.getElementById('manualSubmitBtn').addEventListener('click', function() {
                            const content = document.getElementById('submission_content').value;
                            const formData = new FormData();
                            formData.append('submission_content', content);
                            formData.append('_token', '{{ csrf_token() }}');
                            
                            if (document.getElementById('attachment').files.length > 0) {
                                formData.append('attachment', document.getElementById('attachment').files[0]);
                            }
                            
                            console.log('Manual submission to actual route...');
                            console.log('Target URL:', '{{ route("student.assessments.submit", $assessment) }}');
                            
                            fetch('{{ route("student.assessments.submit", $assessment) }}', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => {
                                console.log('Response status:', response.status);
                                console.log('Response headers:', response.headers);
                                if (response.redirected) {
                                    console.log('Response redirected to:', response.url);
                                    window.location.href = response.url;
                                } else {
                                    return response.text();
                                }
                            })
                            .then(data => {
                                if (data) {
                                    console.log('Response data:', data);
                                    alert('Manual submission completed! Check console for details.');
                                }
                            })
                            .catch(error => {
                                console.error('Manual submission error:', error);
                                alert('Manual submission failed! Check console for details.');
                            });
                        });
                        
                        // Test if the form can be found
                        console.log('Assessment form found:', document.getElementById('assessmentForm'));
                        console.log('Submit button found:', document.getElementById('submitBtn'));
                        console.log('Test button found:', document.getElementById('testBtn'));
                        console.log('Manual submit button found:', document.getElementById('manualSubmitBtn'));
                        console.log('Simple submit button found:', document.getElementById('simpleSubmitBtn'));
                        
                        // Simple submit button - direct form submission
                        document.getElementById('simpleSubmitBtn').addEventListener('click', function() {
                            console.log('Simple submit button clicked');
                            const form = document.getElementById('assessmentForm');
                            const content = document.getElementById('submission_content').value;
                            
                            if (!content.trim()) {
                                alert('Please enter your answer before submitting.');
                                return;
                            }
                            
                            console.log('Submitting form directly...');
                            console.log('Form action:', form.action);
                            console.log('Form method:', form.method);
                            
                            // Try to submit the form directly
                            try {
                                form.submit();
                                console.log('Form.submit() called successfully');
                            } catch (error) {
                                console.error('Error calling form.submit():', error);
                                alert('Error submitting form: ' + error.message);
                            }
                        });
                        
                        // Test form submission manually
                        setTimeout(() => {
                            const form = document.getElementById('assessmentForm');
                            if (form) {
                                console.log('Testing manual form submission...');
                                const submitEvent = new Event('submit', { bubbles: true, cancelable: true });
                                form.dispatchEvent(submitEvent);
                            }
                        }, 1000);
                        </script>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout> 
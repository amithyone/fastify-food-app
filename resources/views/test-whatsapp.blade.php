<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WhatsApp Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen p-4">
    <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-6">
        <h1 class="text-2xl font-bold text-center mb-6">WhatsApp Verification Test</h1>
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                <input type="tel" id="phoneNumber" placeholder="08012345678" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
            </div>
            
            <button id="sendCodeBtn" 
                    class="w-full bg-green-500 hover:bg-green-600 text-white py-3 px-4 rounded-lg font-semibold transition duration-200 flex items-center justify-center gap-2"
                    style="background-color: #10b981 !important; color: white !important;">
                <i class="fab fa-whatsapp text-lg"></i>
                Send Verification Code
            </button>
            
            <div id="result" class="mt-4 p-4 bg-gray-50 rounded-lg hidden">
                <h3 class="font-semibold mb-2">API Response:</h3>
                <pre id="responseText" class="text-sm bg-white p-2 rounded border overflow-auto"></pre>
            </div>
            
            <div id="debugCode" class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg hidden">
                <h3 class="font-semibold text-yellow-800 mb-2">Debug Code:</h3>
                <p id="codeText" class="text-2xl font-mono text-center text-yellow-800"></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('sendCodeBtn').addEventListener('click', async function() {
            const phoneNumber = document.getElementById('phoneNumber').value.trim();
            const resultDiv = document.getElementById('result');
            const responseText = document.getElementById('responseText');
            const debugCodeDiv = document.getElementById('debugCode');
            const codeText = document.getElementById('codeText');
            
            if (!phoneNumber) {
                alert('Please enter a phone number');
                return;
            }
            
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            try {
                const response = await fetch('/api/phone/send-code', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        phone_number: phoneNumber,
                        is_login: false
                    })
                });
                
                const data = await response.json();
                
                // Show full response
                resultDiv.classList.remove('hidden');
                responseText.textContent = JSON.stringify(data, null, 2);
                
                // Show debug code if available
                if (data.debug_code) {
                    debugCodeDiv.classList.remove('hidden');
                    codeText.textContent = data.debug_code;
                }
                
                console.log('API Response:', data);
                
            } catch (error) {
                console.error('Error:', error);
                resultDiv.classList.remove('hidden');
                responseText.textContent = 'Error: ' + error.message;
            } finally {
                this.disabled = false;
                this.innerHTML = '<i class="fab fa-whatsapp text-lg"></i> Send Verification Code';
            }
        });
    </script>
</body>
</html> 
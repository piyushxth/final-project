<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    @php
    if(isset($title)){
    $title = $title.' | '.env('APP_NAME');
    } else {
    $title = env('APP_NAME');
    }
    @endphp {{$title}}</title>
  @yield('seo')
  <link rel="shortcut icon" href="{{ asset('images/favicon.png') }}" />
  <!-- custom css  -->
  <link rel="stylesheet" href="{{ asset('frontend/css/style.css') }}" />
  <!-- fontwsome css  -->
  <link rel="stylesheet" href="{{ asset('frontend/css/all.min.css') }}" />
  <!-- slick slider css  -->
  <link rel="stylesheet" href="{{ asset('frontend/css/slick.css') }}" />
  <!-- bootstrap css  -->
  <link rel="stylesheet" href="{{ asset('frontend/css/bootstrap.min.css') }}" />
  <!-- animated css  -->
  <link rel="stylesheet" href="{{ asset('frontend/css/animate.css') }}" />
  <!-- toastr.min.css -->
  <link rel="stylesheet" href="{{ asset('frontend/css/toastr.min.css') }}" />
  <!-- picZoomer css  -->
  <link rel="stylesheet" href="{{ asset('frontend/css/jquery-picZoomer.css') }}" />
  {{-- Share Starts --}}
  <script type='text/javascript' src='https://platform-api.sharethis.com/js/sharethis.js#property=635621359dc3400019b695f3&product=sop' async='async'></script>
  {{-- Share Ends --}}
</head>
@php($bodyClass = isset($bodyClass) ? $bodyClass : '')
@php($loggedinClass = ($user_account != '' || $user_account != NULL) ? 'loggedin' : '')

<body class="{{ $bodyClass }} {{ $loggedinClass }}" data-siteurl="{{ url('/') }}">
  <script>
    console.log('Body loaded, site URL:', "{{ url('/') }}");
  </script>
  @include('frontend.layouts.header')
  @yield('content')
  @include('frontend.layouts.footer')
  
  <!-- Chatbox -->
  <div id="chatbox-container" class="chatbox-container">
    <div id="chatbox-toggle" class="chatbox-toggle">
      <i class="fas fa-comments"></i>
    </div>
    <div id="chatbox" class="chatbox">
      <div class="chatbox-header">
        <h4>Shopping Assistant</h4>
        <button id="chatbox-close" class="chatbox-close">×</button>
      </div>
      <div class="chatbox-body">
        <div class="chat-messages" id="chat-messages">
          <div class="message bot-message">
            <div class="message-content">
              Hello! I'm your shopping assistant. How can I help you today?
            </div>
          </div>
        </div>
      </div>
      <div class="chatbox-footer">
        <form id="chat-form" class="chat-form">
          <input type="text" id="chat-input" placeholder="Type your message..." required>
          <button type="submit" id="chat-send"><i class="fas fa-paper-plane"></i></button>
        </form>
      </div>
    </div>
  </div>
  <!-- End Chatbox -->
  
  <script>
    // Chatbox functionality
    document.addEventListener('DOMContentLoaded', function() {
      const chatboxContainer = document.getElementById('chatbox-container');
      const chatboxToggle = document.getElementById('chatbox-toggle');
      const chatbox = document.getElementById('chatbox');
      const chatboxClose = document.getElementById('chatbox-close');
      const chatForm = document.getElementById('chat-form');
      const chatInput = document.getElementById('chat-input');
      const chatMessages = document.getElementById('chat-messages');
      
      // Toggle chatbox visibility
      chatboxToggle.addEventListener('click', function() {
        chatbox.classList.toggle('active');
      });
      
      // Close chatbox
      chatboxClose.addEventListener('click', function() {
        chatbox.classList.remove('active');
      });
      
      // Handle chat form submission
      chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const message = chatInput.value.trim();
        if (message) {
          // Add user message to chat
          addMessage(message, 'user');
          
          // Clear input
          chatInput.value = '';
          
          // Scroll to bottom
          chatMessages.scrollTop = chatMessages.scrollHeight;
          
          // Send message to server
          sendMessageToServer(message);
        }
      });
      
      // Add message to chat
      function addMessage(content, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.classList.add('message');
        messageDiv.classList.add(sender + '-message');
        
        const messageContent = document.createElement('div');
        messageContent.classList.add('message-content');
        messageContent.textContent = content;
        
        messageDiv.appendChild(messageContent);
        chatMessages.appendChild(messageDiv);
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
      }
      
      // Send message to server
      function sendMessageToServer(message) {
        // Show typing indicator
        const typingIndicator = document.createElement('div');
        typingIndicator.classList.add('message', 'bot-message');
        typingIndicator.id = 'typing-indicator';
        typingIndicator.innerHTML = '<div class="message-content">Typing...</div>';
        chatMessages.appendChild(typingIndicator);
        
        // Scroll to bottom
        chatMessages.scrollTop = chatMessages.scrollHeight;
        
        // Send AJAX request
        fetch("{{ route('frontend.chat') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
          // Remove typing indicator
          document.getElementById('typing-indicator').remove();
          
          // Add bot response
          if (data.status === 'success') {
            addMessage(data.message, 'bot');
          } else {
            addMessage('Sorry, I encountered an error. Please try again.', 'bot');
          }
        })
        .catch(error => {
          // Remove typing indicator
          document.getElementById('typing-indicator').remove();
          
          // Add error message
          addMessage('Sorry, I encountered an error. Please try again.', 'bot');
        });
      }
    });
  </script>
</body>
<!-- jquery  -->
<script type="text/javascript" src="{{ asset('frontend/js/jquery-3.6.0.min.js') }}"></script>
<!-- bootstrap  -->
<script type="text/javascript" src="{{ asset('frontend/js/bootstrap.bundle.min.js') }}"></script>
<!-- slck slider  -->
<script type="text/javascript" src="{{ asset('frontend/js/slick.min.js') }}"></script>
<!-- picZoomer  -->
<script type="text/javascript" src="{{ asset('frontend/js/jquery.picZoomer.js') }}"></script>
<!--wow js  -->
<script type="text/javascript" src="{{ asset('frontend/js/wow.min.js') }}"></script>
<!-- way-point js  -->
<script type="text/javascript" src="{{ asset('frontend/js/jquery.waypoints.min.js') }}"></script>
<!-- toastr.min.js -->
<script type="text/javascript" src="{{ asset('frontend/js/toastr.min.js') }}"></script>
<!-- custom js file  -->
<script type="text/javascript" src="{{ asset('frontend/js/main.js') }}"></script>
<script>
  console.log('All scripts loaded');
  // Test if jQuery is available
  if (typeof jQuery !== 'undefined') {
    console.log('jQuery version:', jQuery.fn.jquery);
    // Test search functionality
    if ($('.search-icon').length > 0) {
      console.log('Search icon found');
    } else {
      console.log('Search icon not found');
    }
  } else {
    console.log('jQuery is not loaded');
  }
</script>
@yield('script')

</html>
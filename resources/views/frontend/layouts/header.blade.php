<header>
    <div class="main-header ">
        <nav class="navbar navbar-expand-lg navbar-light bg-light ">
            <div class="container">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <figure><img height='20'  src="{{ asset('frontend/images/logo.png') }}" alt="{{ env('APP_NAME') }}"></figure>
                </a>
                <!-- search bar for responsive design  -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                    data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                    aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"><i class="fas fa-bars"></i></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('/') ? 'active' : '' }}"
                                href="{{ route('home') }}">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('about-us') ? 'active' : '' }}"
                                href="{{ route('frontend.aboutus') }}">About us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}"
                                href="{{ route('contact') }}">
                                Contact us
                            </a>
                        </li>
                    </ul>
                    <style>
                        .nav-Search-bar {
                            display: block !important;
                            margin-left: 15px;
                            margin-right: 15px;
                            visibility: visible !important;
                        }
                        .search-box-wrapper {
                            position: relative;
                        }
                        .search-box {
                            position: absolute;
                            top: 100%;
                            right: 0;
                            z-index: 1000;
                            background: white;
                            border: 1px solid #ccc;
                            border-radius: 4px;
                            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                            width: 400px;
                            padding: 10px;
                            margin-top: 5px;
                        }
                        .search-results {
                            width: 400px;
                        }
                        .search-results .search-result-item {
                            transition: background-color 0.2s;
                            border-bottom: 1px solid #eee;
                        }
                        .search-results .search-result-item:hover {
                            background-color: #f8f9fa;
                        }
                        .search-results .search-result-item a {
                            display: flex;
                            align-items: center;
                            text-decoration: none;
                            color: #333;
                            padding: 10px;
                        }
                        .search-results .search-result-item img {
                            width: 50px;
                            height: 50px;
                            object-fit: cover;
                            margin-right: 10px;
                        }
                        .search-results .product-info {
                            flex: 1;
                        }
                        .search-results .product-name {
                            font-weight: bold;
                            margin-bottom: 3px;
                        }
                        .search-results .product-description {
                            font-size: 12px;
                            color: #666;
                            margin-bottom: 3px;
                        }
                        .search-results .product-price {
                            font-size: 14px;
                            color: #991b1c;
                        }
                        .search-results .no-image {
                            width: 50px;
                            height: 50px;
                            background: #f0f0f0;
                            margin-right: 10px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                        }
                        .search-results .no-image i {
                            color: #999;
                        }
                        .search-results .no-results {
                            padding: 10px;
                            text-align: center;
                            color: #666;
                        }
                        .search-icon {
                            cursor: pointer;
                            font-size: 20px;
                            z-index: 9999;
                            position: relative;
                            display: inline-block !important;
                            visibility: visible !important;
                        }
                    </style>
                    <div class="nav-Search-bar">
                        <div class="search-box-wrapper">
                            <i class="fas fa-search search-icon" style="cursor: pointer; font-size: 20px; z-index: 9999; position: relative;" onclick="console.log('Search icon clicked directly'); toggleSearchBox();"></i>
                            <div class="search-box" style="display: none; position: absolute; top: 100%; right: 0; z-index: 999999; background: white; border: 1px solid #ccc; border-radius: 4px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); width: 400px; padding: 10px; margin-top: 5px; overflow: visible;">
                                <form action="{{ route('frontend.site_search') }}" method="GET" class="search-form" onsubmit="console.log('Form submitted'); return false;">
                                    <input type="text" name="keywords" placeholder="Search for products..." autocomplete="off" onkeyup="console.log('Key up event:', this.value);" onkeydown="console.log('Key down event:', this.value);">
                                    <input type="submit" value="Search">
                                </form>
                                <div class="search-results" style="display: none; background: white; border: 1px solid #ccc; max-height: 300px; overflow-y: auto; position: absolute; width: 400px; z-index: 999; box-shadow: 0 4px 6px rgba(0,0,0,0.1); margin-top: 5px;">
                                    <!-- Search results will be populated here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Test link for debugging -->
                    <a href="#" onclick="toggleSearchBox(); return false;" style="margin-left: 10px; color: #007bff; text-decoration: underline;">Test Search</a>
                    <button onclick="manualSearchTest()" style="margin-left: 10px; padding: 5px 10px; background: #007bff; color: white; border: none; border-radius: 3px;">Manual Search Test</button>
                    <button onclick="testAjaxSearch()" style="margin-left: 10px; padding: 5px 10px; background: #28a745; color: white; border: none; border-radius: 3px;">Test AJAX Search</button>
                    <script>
                        function manualSearchTest() {
                            console.log('Manual search test triggered');
                            // Show the search box
                            var searchBox = document.querySelector('.search-box');
                            if (searchBox) {
                                searchBox.style.display = 'block';
                                // Focus on the input field
                                var inputField = searchBox.querySelector('input[name="keywords"]');
                                if (inputField) {
                                    inputField.focus();
                                    // Simulate typing
                                    inputField.value = 'test';
                                    // Trigger keyup event
                                    var event = new Event('keyup');
                                    inputField.dispatchEvent(event);
                                }
                            }
                        }

                        // Test AJAX search function
                        function testAjaxSearch() {
                            console.log('Testing AJAX search');
                            var baseUrl = document.body.getAttribute('data-siteurl');
                            console.log('Base URL:', baseUrl);
                            
                            if (baseUrl) {
                                var searchUrl = baseUrl + '/test-search';
                                console.log('Search URL:', searchUrl);
                                
                                // Show search results container
                                var searchResults = document.querySelector('.search-results');
                                if (searchResults) {
                                    searchResults.style.display = 'block';
                                    searchResults.innerHTML = '<div style="padding: 10px; text-align: center;"><i class="fas fa-spinner fa-spin"></i> Testing search...</div>';
                                }
                                
                                // Make a simple AJAX request
                                fetch(searchUrl)
                                    .then(response => {
                                        console.log('Response received:', response);
                                        return response.json();
                                    })
                                    .then(data => {
                                        console.log('Search data received:', data);
                                        if (searchResults) {
                                            searchResults.innerHTML = '<div style="padding: 10px;">Test successful - search endpoint is working: ' + data.status + '</div>';
                                        }
                                    })
                                    .catch(error => {
                                        console.log('Search error:', error);
                                        if (searchResults) {
                                            searchResults.innerHTML = '<div style="padding: 10px; color: red;">Error: ' + error.message + '</div>';
                                        }
                                    });
                            }
                        }
                    </script>
                    <script>
                        // Function to toggle search box visibility
                        function toggleSearchBox() {
                            console.log('toggleSearchBox function called');
                            var searchBox = document.querySelector('.search-box');
                            console.log('Search box element:', searchBox);
                            if (searchBox) {
                                console.log('Current display style:', searchBox.style.display);
                                if (searchBox.style.display === 'block') {
                                    searchBox.style.display = 'none';
                                    console.log('Search box hidden');
                                } else {
                                    searchBox.style.display = 'block';
                                    console.log('Search box shown');
                                    // Focus on the input field
                                    var inputField = searchBox.querySelector('input[name="keywords"]');
                                    if (inputField) {
                                        inputField.focus();
                                        console.log('Input field focused');
                                    }
                                }
                            } else {
                                console.log('Search box element not found');
                            }
                        }

                        // Ensure search functionality is initialized
                        function initializeSearch() {
                            console.log('initializeSearch function called');
                            if (typeof jQuery !== 'undefined') {
                                console.log('jQuery is available');
                                // Search icon click event
                                $('.search-icon').on('click', function(e) {
                                    console.log('Search icon clicked via jQuery');
                                    e.stopPropagation();
                                    $('.search-box').toggle();
                                    $('.search-box input[name="keywords"]').focus();
                                });

                                // Close search when clicking outside
                                $(document).on('click', function(e) {
                                    if (!$(e.target).closest('.nav-Search-bar').length) {
                                        $('.search-box').hide();
                                        $('.search-results').hide();
                                    }
                                });

                                // Prevent form submission and use AJAX instead
                                $('.search-form').on('submit', function(e) {
                                    e.preventDefault();
                                    var keywords = $(this).find('input[name="keywords"]').val();
                                    var baseUrl = $("body").attr("data-siteurl");
                                    
                                    if (keywords.length >= 2) {
                                        window.location.href = baseUrl + '/search?keywords=' + encodeURIComponent(keywords);
                                    }
                                });

                                // Live search functionality
                                $('.search-box input[name="keywords"]').on('keyup', function() {
                                    var keywords = $(this).val();
                                    var baseUrl = $("body").attr("data-siteurl");
                                    
                                    console.log('Search keywords:', keywords);
                                    
                                    if (keywords.length >= 2) {
                                        $.ajax({
                                            url: baseUrl + '/search',
                                            type: 'GET',
                                            data: { keywords: keywords },
                                            beforeSend: function() {
                                                $('.search-results').show();
                                                $('.search-results').html('<div style="padding: 10px; text-align: center;"><i class="fas fa-spinner fa-spin"></i> Searching...</div>');
                                            },
                                            success: function(data) {
                                                console.log('Search results received');
                                                $('.search-results').html(data);
                                                $('.search-results').show();
                                            },
                                            error: function(xhr, status, error) {
                                                console.log('Search error:', error);
                                                $('.search-results').html('<div style="padding: 10px; text-align: center; color: #666;">Error occurred while searching</div>');
                                            }
                                        });
                                    } else {
                                        $('.search-results').hide();
                                    }
                                });
                            } else {
                                console.log('jQuery is not available');
                            }
                        }

                        // Initialize search when DOM is ready
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initializeSearch);
                        } else {
                            // DOM is already ready
                            initializeSearch();
                        }
                    </script>
                    @include('frontend.layouts.auth')
                    <div class="profile-wishlist-cart">
                        <div class="profile loggedin_menu" style="display: none;">
                            <a href="{{ route('customer.dashboard') }}"> <i class="fas fa-user"></i></a>
                        </div>
                        <div class="wishlist loggedin_menu" style="display: none;">
                            <a class="position-relative" href="{{ route('customer.wishlist.index') }}">
                                <i class="fas fa-heart"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge "
                                    id="wishlist-count">
                                    {{ $wishlist_count }}
                                </span>
                            </a>
                        </div>
                        <div class="cart loggedin_menu" style="display: none;">
                            <a class="position-relative" href="{{ route('customer.cart.index') }}">
                                <i class="fas fa-shopping-cart"></i>
                                <span class="position-absolute top-0 start-100 translate-middle badge"
                                    id="cart-count">
                                    {{ $cart_count }}
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
</header>

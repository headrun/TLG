
<!doctype html>
<!--[if lte IE 9]> <html class="lte-ie9" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--> <html lang="en"> <!--<![endif]-->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <!-- Remove Tap Highlight on Windows Phone IE -->
    <meta name="msapplication-tap-highlight" content="no"/>

    <link rel="icon" type="image/png" href="{{url()}}/assets/img/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="{{url()}}/assets/img/favicon-32x32.png" sizes="32x32">

    <title>Inspire</title>


    <!-- uikit -->
    <link rel="stylesheet" href="{{url()}}/bower_components/uikit/css/uikit.almost-flat.min.css" media="all">

    <!-- flag icons -->
    <link rel="stylesheet" href="{{url()}}/assets/icons/flags/flags.min.css" media="all">

    <!-- altair admin -->
    <link rel="stylesheet" href="{{url()}}/assets/css/main.min.css" media="all">
    <link rel="stylesheet" href="{{url()}}/assets/css/style.css" media="all">
    
    <link rel="stylesheet" href="{{url()}}/assets/breadcrumbs/css/global.css" media="all">
    
    <!-- matchMedia polyfill for testing media queries in JS -->
    <!--[if lte IE 9]>
        <script type="text/javascript" src="bower_components/matchMedia/matchMedia.js"></script>
        <script type="text/javascript" src="bower_components/matchMedia/matchMedia.addListener.js"></script>
    <![endif]-->
    
    
    
    
    
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	
	
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">
	
	
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
	
	
	@yield('libraryCSS')
	<style>
		.has-error .form-control {
		    border-color: #a94442 !important;
		    /* background-color: #F1CCCC !important; */
		    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
		    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
		}
		.has-error .form-control {
		    border-color: #a94442;
		    -webkit-box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
		    box-shadow: inset 0 1px 1px rgba(0,0,0,.075);
		}

    #commonSearchTxt::selection {
      #commonSearchTxt::placeholder{
        color:white;  
      }
      
    }
	
	</style>
	 
   
	<!-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script> -->
	
</head>
<body class="sidebar_main_open sidebar_main_swipe">
    <!-- main header -->
    <header id="header_main">
        <div class="header_main_content">
            <nav class="uk-navbar">
                <!-- main sidebar switch -->
                <a href="#" id="sidebar_main_toggle" class="sSwitch sSwitch_left">
                    <span class="sSwitchIcon"></span>
                </a>
                <!-- secondary sidebar switch -->
                <a href="#" id="sidebar_secondary_toggle" class="sSwitch sSwitch_right sidebar_secondary_check">
                    <span class="sSwitchIcon"></span>
                </a>
                
                <div class="" style="display: block; padding-top:10px;">
		            <!-- <i class="md-icon header_main_search_close material-icons">&#xE5CD;</i> -->
		            <form class="uk-form" id="profileSearchForm" action="{{url()}}/quick/navigateToProfile" method="post">
		                <input type="text" id="commonSearchTxt" name="term" class="headerSearchInput" placeholder="Search" />
		                <input type="hidden"  id="idCommonSearchTxt" name="idCommonSearchTxt" class="" />
		                <button  style="display: none;" id="profileSearchFormSubmitBtn" class="buttonSearch uk-button-link" type="submit">
		                	<i class="md-icon material-icons" style="color: #FFF;">&#xE8B6;</i>
		                </button>
		            </form>
		</div>
                <div class="" style="display: block; padding-top:10px; color: white;">
                    
                </div>
                <div class="uk-navbar-flip">
                    <ul class="uk-navbar-nav user_actions">
                    	<!-- <li>
                    		<div class="header_main_search_form">
					            <i class="md-icon header_main_search_close material-icons">&#xE5CD;</i>
					            <form class="uk-form">
					                <input type="text" id="commonSearchTxt" name="term" class="header_main_search_input" />
					                <button class="header_main_search_btn uk-button-link">
					                	<i class="md-icon material-icons">&#xE8B6;</i>
					                </button>
					            </form>
					        </div>
                    	</li> -->
                        <!-- <li><a href="#" id="main_search_btn" class="user_action_icon"><i class="material-icons md-24 md-light">&#xE8B6;</i></a></li> -->
                        
                        <li data-uk-dropdown="{mode:'click'}">
                            <a href="#" class="user_action_image" style="margin-top: -20px;">
                             <label style="padding: 5px;">{{ Session::get('firstName') }}{{ Session::get('lastName') }}</label>
                                <img class="md-user-image" src="{{url()}}/assets/img/avatars/avatar_11_tn.png" alt="" title={{ Session::get('firstName') }}{{ Session::get('lastName') }}  /> 
                            
                            </a>
                            <div class="uk-dropdown uk-dropdown-small uk-dropdown-flip">
                                <ul class="uk-nav js-uk-prevent">
                                    <!-- <li><a href="page_user_profile.html">My profile</a></li>
                                    <li><a href="page_settings.html">Settings</a></li> -->
                                    <li><a href="{{url()}}/vault/logout">Logout</a></li>
                                </ul>
                            </div>
                        </li>
                    </ul>
                </div>
            </nav>
        </div>
        
        
        <!-- <div class="header_main_search_form">
            <i class="md-icon header_main_search_close material-icons">&#xE5CD;</i>
            <form class="uk-form">
                <input type="text" id="commonSearchTxt" name="term" class="header_main_search_input" />
                <button class="header_main_search_btn uk-button-link">
                	<i class="md-icon material-icons">&#xE8B6;</i>
                </button>
            </form>
        </div> -->
    </header><!-- main header end -->

   @include('layout.sidebar')
    
  <div id="page_content">
        <div id="page_content_inner">
        
        	@yield('content')
        
         </div>
    </div>

    <!-- google web fonts -->
    <script>
        WebFontConfig = {
            google: {
                families: [
                    'Source+Code+Pro:400,700:latin',
                    'Roboto:400,300,500,700,400italic:latin'
                ]
            }
        };
        (function() {
            var wf = document.createElement('script');
            wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
            '://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
            wf.type = 'text/javascript';
            wf.async = 'true';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(wf, s);
        })();
    </script>

   

<div id="style_switcher">
    <div id="style_switcher_toggle"><i class="material-icons">&#xE8B8;</i></div>
    <div class="uk-margin-medium-bottom">
        <h4 class="heading_c uk-margin-bottom">Colors</h4>
        <ul class="switcher_app_themes" id="theme_switcher">
            <li class="app_style_default active_theme" data-app-theme="">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_a" data-app-theme="app_theme_a">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_b" data-app-theme="app_theme_b">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_c" data-app-theme="app_theme_c">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_d" data-app-theme="app_theme_d">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_e" data-app-theme="app_theme_e">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_f" data-app-theme="app_theme_f">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
            <li class="switcher_theme_g" data-app-theme="app_theme_g">
                <span class="app_color_main"></span>
                <span class="app_color_accent"></span>
            </li>
        </ul>
    </div>
    <div class="uk-visible-large">
        <h4 class="heading_c">Sidebar</h4>
        <p>
            <input type="checkbox" name="style_sidebar_mini" id="style_sidebar_mini" data-md-icheck />
            <label for="style_sidebar_mini" class="inline-label">Mini Sidebar</label>
        </p>
    </div>
</div>
	
	<!-- common functions -->
    <script src="{{url()}}/assets/js/common.min.js"></script>
	<!-- uikit functions -->
    <script src="{{url()}}/assets/js/uikit_custom.min.js"></script>
    <!-- altair common functions/helpers -->
    
    <script src="{{url()}}/assets/js/altair_admin_common.min.js"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script>
	 	$( "#commonSearchTxt" ).autocomplete({
	        source: "{{url()}}/quick/customerStudentSearch",
	        minLength: 3,
	        select: function( event, ui ) {

	        	$( "#commonSearchTxt" ).val(ui.item.value);
	        	$( "#idCommonSearchTxt" ).val(ui.item.id);
            event.preventDefault();
            if($("#idCommonSearchTxt").val() != ""){
            $("#profileSearchForm").submit();
            }
		        
	          console.log( ui.item ?
	            "Selected: " + ui.item.value + " aka " + ui.item.id :
	            "Nothing selected, input was " + this.value );
	        }
	      });


    $("#profileSearchFormSubmitBtn").click(function (event){
      event.preventDefault();      
    });
    /*
	 	$("#profileSearchFormSubmitBtn").click(function (event){

		 	event.preventDefault();

		 	if($("#idCommonSearchTxt").val() != ""){

		 		$("#profileSearchForm").submit();

		 	}




		})
    */


	</script>
	<script type="text/javascript">
		var ajaxUrl = "{{URL::to('/quick/')}}/";
	</script>
    @yield('libraryJS')
    
	

    <script>
        $(function() {
            // enable hires images
            altair_helpers.retina_images();
            // fastClick (touch devices)
            if(Modernizr.touch) {
                FastClick.attach(document.body);
            }
        });
    </script>
    
<script>
    
    $(function() {

        $("#{{$currentPage}}").addClass('act_item');
        $("#{{$mainMenu}}").addClass('current_section act_item');
        $("#{{$mainMenu}}").addClass('current_section act_section');
        $("#{{$mainMenu}}_UL").css('display','block');
        
        var $switcher = $('#style_switcher'),
            $switcher_toggle = $('#style_switcher_toggle'),
            $theme_switcher = $('#theme_switcher'),
            $mini_sidebar_toggle = $('#style_sidebar_mini');

        $switcher_toggle.click(function(e) {
            e.preventDefault();
            $switcher.toggleClass('switcher_active');
        });

        $theme_switcher.children('li').click(function(e) {
            e.preventDefault();
            var $this = $(this),
                this_theme = $this.attr('data-app-theme');

            $theme_switcher.children('li').removeClass('active_theme');
            $(this).addClass('active_theme');
            $('body')
                .removeClass('app_theme_a app_theme_b app_theme_c app_theme_d app_theme_e app_theme_f app_theme_g')
                .addClass(this_theme);

            if(this_theme == '') {
                localStorage.removeItem('altair_theme');
            } else {
                localStorage.setItem("altair_theme", this_theme);
            }

        });

        // change input's state to checked if mini sidebar is active
        if((localStorage.getItem("altair_sidebar_mini") !== null && localStorage.getItem("altair_sidebar_mini") == '1') || $('body').hasClass('sidebar_mini')) {
            $mini_sidebar_toggle.iCheck('check');
        }

        // toggle mini sidebar
        $mini_sidebar_toggle
            .on('ifChecked', function(event){
                $switcher.removeClass('switcher_active');
                localStorage.setItem("altair_sidebar_mini", '1');
                location.reload(true);
            })
            .on('ifUnchecked', function(event){
                $switcher.removeClass('switcher_active');
                localStorage.removeItem('altair_sidebar_mini');
                location.reload(true);
            });

        // hide style switcher
        $document.on('click keyup', function(e) {
            if( $switcher.hasClass('switcher_active') ) {
                if (
                    ( !$(e.target).closest($switcher).length )
                    || ( e.keyCode == 27 )
                ) {
                    $switcher.removeClass('switcher_active');
                }
            }
        });

        if(localStorage.getItem("altair_theme") !== null) {
            $theme_switcher.children('li[data-app-theme='+localStorage.getItem("altair_theme")+']').click();
        }
    });
</script>


</body>
</html>
<!--

 ______                                                 _
|  ___ \                                               | |
| | _ | | ____ ____   ____ _   _    _ _ _  ____  ___   | | _   ____  ____ ____
| || || |/ _  |  _ \ / _  | | | |  | | | |/ _  |/___)  | || \ / _  )/ ___) _  )
| || || ( ( | | | | ( ( | |\ V /   | | | ( ( | |___ |  | | | ( (/ /| |  ( (/ /
|_||_||_|\_||_|_| |_|\_||_| \_/     \____|\_||_(___/   |_| |_|\____)_|   \____)

-->


<!DOCTYPE html>
<html lang="en" data-theme="dracula">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="application-name" content="TL;dr" />
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@300;400;500&display=swap" rel="stylesheet">



    <link rel="icon" href="/images/xxhdpi.png">
    <link rel="apple-touch-icon" href="/images/xxhdpi.png">

    <script src="https://kit.fontawesome.com/0db8c7f53e.js" crossorigin="anonymous"></script>

    @if (app()->environment('production'))
		<!-- Google Tag Manager -->
		<script>
			(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
			new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
			j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
			'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
			})(window,document,'script','dataLayer','GTM-P4F8FN8');
		</script>
		<!-- End Google Tag Manager -->
    @endif

    @vite('resources/js/app.js')
    @inertiaHead
</head>

<body>
	<!-- Google Tag Manager (noscript) -->
		<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-P4F8FN8"
		height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
	<!-- End Google Tag Manager (noscript) -->
    @inertia
</body>

</html>

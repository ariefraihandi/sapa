<head>
    <!-- Title -->
    <title>{{ $title ?? 'SAPA-MS - Sistem Komunikasi & Pelayanan Lintas Mahkamah Syar\'iyah' }}</title>

    <!-- Meta Tags -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="author" content="BILIKMEDIA">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon icon -->
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo/sapa.png') }}">
    
    <!-- Fonts & Icons CDN -->
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v6.5.1/css/all.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS Styles -->
    <style>
        :root {
            --primary-color: #0b6e39;
            --secondary-color: #10b981;
            --black: #000000;
            --white: #ffffff;
            --gray: #f8fafc;
            --gray-2: #64748b;
        }

        * {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100vh;
            overflow: hidden;
            background-color: var(--white);
        }

        .container-auth {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            height: 100vh;
        }

        .col {
            width: 50%;
        }

        .align-items-center {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .form-wrapper {
            width: 100%;
            max-width: 28rem;
        }

        .form {
            padding: 2rem;
            background-color: var(--white);
            border-radius: 1.5rem;
            width: 100%;
            box-shadow: rgba(0, 0, 0, 0.15) 0px 10px 25px;
            transform: scale(0);
            transition: .5s ease-in-out;
            transition-delay: 1s;
        }

        .input-group-custom {
            position: relative;
            width: 100%;
            margin: 1rem 0;
        }

        .input-group-custom i {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            font-size: 1.3rem;
            color: var(--gray-2);
        }

        .input-group-custom input {
            width: 100%;
            padding: 0.85rem 1rem 0.85rem 3rem;
            font-size: 0.95rem;
            background-color: var(--gray);
            border-radius: 0.5rem;
            border: 0.125rem solid #e2e8f0;
            outline: none;
            transition: all 0.3s ease;
        }

        .input-group-custom input:focus {
            border: 0.125rem solid var(--primary-color);
            background-color: var(--white);
        }

        .form button.btn-auth {
            cursor: pointer;
            width: 100%;
            padding: 0.75rem 0;
            border-radius: 0.5rem;
            border: none;
            background-color: var(--primary-color);
            color: var(--white);
            font-size: 1rem;
            font-weight: 600;
            outline: none;
            transition: background-color 0.3s ease;
        }

        .form button.btn-auth:hover {
            background-color: #08542b;
        }

        .form p {
            margin: 1rem 0 0 0;
            font-size: 0.8rem;
            color: var(--gray-2);
        }

        .flex-col {
            flex-direction: column;
        }

        .pointer {
            cursor: pointer;
            color: var(--primary-color);
        }

        .container-auth.sign-in .form.sign-in,
        .container-auth.sign-up .form.sign-up {
            transform: scale(1);
        }

        .content-row {
            position: absolute;
            top: 0;
            left: 0;
            pointer-events: none;
            z-index: 6;
            width: 100%;
        }

        .text {
            margin: 2rem;
            color: var(--white);
        }

        .text h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            transition: 1s ease-in-out;
        }

        .text p {
            font-weight: 400;
            font-size: 0.9rem;
            transition: 1s ease-in-out;
            transition-delay: .2s;
        }

        .text.sign-in h2, .text.sign-in p {
            transform: translateX(-250%);
        }

        .text.sign-up h2, .text.sign-up p {
            transform: translateX(250%);
        }

        .container-auth.sign-in .text.sign-in h2,
        .container-auth.sign-in .text.sign-in p,
        .container-auth.sign-up .text.sign-up h2,
        .container-auth.sign-up .text.sign-up p {
            transform: translateX(0);
        }

        /* BACKGROUND ANIMATION DESKTOP */
        .container-auth::before {
            content: "";
            position: absolute;
            top: 0;
            right: 0;
            height: 100vh;
            width: 300vw;
            transform: translate(35%, 0);
            background-image: linear-gradient(-45deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            transition: 1s ease-in-out;
            z-index: 6;
            box-shadow: rgba(0, 0, 0, 0.25) 0px 5px 15px;
            border-bottom-right-radius: max(50vw, 50vh);
            border-top-left-radius: max(50vw, 50vh);
        }

        .container-auth.sign-in::before {
            transform: translate(0, 0);
            right: 50%;
        }

        .container-auth.sign-up::before {
            transform: translate(100%, 0);
            right: 50%;
        }

        /* RESPONSIVE MOBILE / TABLET PORTRAIT (MAX-WIDTH: 768PX) */
        @media only screen and (max-width: 768px) {
            html, body {
                overflow-x: hidden;
                overflow-y: auto;
                height: 100vh;
            }

            /* 1. Reset & Ubah Background Hijau Jadi Banner Atas */
            .container-auth::before,
            .container-auth.sign-in::before,
            .container-auth.sign-up::before {
                content: "";
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                width: 100vw;
                height: 28vh;
                border-radius: 0 0 2.5rem 2.5rem;
                transform: none !important;
                z-index: 2;
                transition: background-color 0.5s ease;
            }

            /* 2. Laying Out Form Vertikal */
            .row {
                display: flex;
                flex-direction: column;
                height: 100vh;
                position: relative;
            }

            .col {
                width: 100% !important;
                position: absolute;
                top: 0;
                left: 0;
                height: 100vh;
                display: flex;
                align-items: flex-end;
                justify-content: center;
                padding: 1rem;
                padding-bottom: 2rem;
                z-index: 10;
                transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.5s ease;
            }

            /* Slide Effect Pada Form Mobile */
            .container-auth .col.sign-in,
            .container-auth .col.sign-up {
                opacity: 0;
                pointer-events: none;
                transform: translateY(20px);
            }

            .container-auth.sign-in .col.sign-in,
            .container-auth.sign-up .col.sign-up {
                opacity: 1;
                pointer-events: all;
                transform: translateY(0);
            }

            /* 3. Card Form Penyesuaian Mobile */
            .form-wrapper {
                max-width: 100%;
                width: 100%;
            }

            .form {
                transform: scale(1) !important;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
                padding: 1.5rem 1.25rem;
                border-radius: 1.25rem;
                background-color: var(--white);
            }

            /* 4. Teks Overlay Header Atas */
            .content-row {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 25vh;
                z-index: 5;
                pointer-events: none;
            }

            .content-row .col {
                align-items: center;
                justify-content: center;
                padding: 0 1rem;
                height: 100%;
                top: 0;
            }

            .text {
                margin: 0 !important;
                text-align: center;
            }

            .text h2 {
                font-size: 1.4rem !important;
                margin-bottom: 0.2rem !important;
                color: #ffffff;
            }

            .text p {
                display: block !important;
                font-size: 0.75rem !important;
                color: rgba(255, 255, 255, 0.9);
                font-weight: 400;
            }

            .text.sign-in h2, .text.sign-in p,
            .text.sign-up h2, .text.sign-up p {
                transform: none !important;
                transition: opacity 0.4s ease;
            }

            .container-auth .text.sign-in,
            .container-auth .text.sign-up {
                opacity: 0;
            }

            .container-auth.sign-in .text.sign-in,
            .container-auth.sign-up .text.sign-up {
                opacity: 1;
            }
            
        }
        .swal2-container .bootstrap-select {
            display: none !important;
        }
    </style>
</head>
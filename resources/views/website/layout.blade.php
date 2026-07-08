<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $site['description'] ?? 'MetwLogistic' }}">
    <title>{{ $pageTitle ?? 'MetwLogistic' }}</title>

    {{-- Google Font (fallback) – replace with your own El Maraei font if self‑hosted --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800;900&display=swap" rel="stylesheet">

    {{-- Custom font-face for El Maraei (adjust path to your font files) --}}
    <style>
        @font-face {
            font-family: 'El Maraei';
            src: url('/fonts/ElMaraei-Regular.woff2') format('woff2'),
                 url('/fonts/ElMaraei-Regular.woff') format('woff');
            font-weight: 400;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'El Maraei';
            src: url('/fonts/ElMaraei-Bold.woff2') format('woff2'),
                 url('/fonts/ElMaraei-Bold.woff') format('woff');
            font-weight: 700;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'El Maraei';
            src: url('/fonts/ElMaraei-ExtraBold.woff2') format('woff2'),
                 url('/fonts/ElMaraei-ExtraBold.woff') format('woff');
            font-weight: 800;
            font-style: normal;
            font-display: swap;
        }
        @font-face {
            font-family: 'El Maraei';
            src: url('/fonts/ElMaraei-Black.woff2') format('woff2'),
                 url('/fonts/ElMaraei-Black.woff') format('woff');
            font-weight: 900;
            font-style: normal;
            font-display: swap;
        }

        :root {
            /* -------- Brand Colors -------- */
            --orange: #ff6b16;
            --orange-dark: #e95604;
            --orange-light: #ffa36b;
            --purple: #55217d;
            --purple-dark: #3d175b;
            --purple-light: #8c4fc9;
            --ink: #1d1830;
            --muted: #6d687c;
            --soft-bg: #fcf6f2;
            --white: #ffffff;
            --shadow-sm: 0 10px 30px rgba(39, 18, 65, 0.08);
            --shadow-md: 0 20px 50px rgba(39, 18, 65, 0.12);
            --shadow-lg: 0 30px 70px rgba(39, 18, 65, 0.18);
            --radius-sm: 16px;
            --radius-md: 24px;
            --radius-lg: 40px;
            --transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* -------- Reset & Base -------- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'El Maraei', 'Tajawal', Tahoma, Arial, sans-serif;
            background: var(--soft-bg);
            color: var(--ink);
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden;
        }

        a {
            color: inherit;
            text-decoration: none;
            transition: color var(--transition);
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: min(1200px, calc(100% - 40px));
            margin: 0 auto;
        }

        /* -------- Typography Helpers -------- */
        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(85, 33, 125, 0.12);
            padding: 6px 18px 6px 22px;
            border-radius: 999px;
            font-weight: 800;
            font-size: 0.85rem;
            color: var(--purple);
            letter-spacing: 0.02em;
            box-shadow: var(--shadow-sm);
        }
        .section-tag .dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--orange);
            display: inline-block;
        }

        .section-title {
            font-size: clamp(2.2rem, 5vw, 3.8rem);
            font-weight: 900;
            color: var(--purple);
            line-height: 1.15;
            letter-spacing: -0.02em;
            margin-top: 12px;
        }

        .section-sub {
            color: var(--muted);
            font-size: 1.1rem;
            max-width: 680px;
            line-height: 1.9;
        }

        /* -------- Buttons -------- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 14px 28px;
            border-radius: 999px;
            font-family: inherit;
            font-weight: 800;
            font-size: 1rem;
            border: 2px solid transparent;
            cursor: pointer;
            transition: all var(--transition);
            letter-spacing: 0.02em;
            position: relative;
            overflow: hidden;
            white-space: nowrap;
        }
        .btn::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.15);
            transform: scaleX(0);
            transform-origin: right;
            transition: transform 0.4s ease;
        }
        .btn:hover::after {
            transform: scaleX(1);
        }

        .btn-primary {
            background: var(--orange);
            color: #fff;
            box-shadow: 0 14px 28px rgba(255, 107, 22, 0.35);
        }
        .btn-primary:hover {
            background: var(--orange-dark);
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(255, 107, 22, 0.4);
        }

        .btn-ghost {
            background: var(--white);
            color: var(--purple);
            border-color: rgba(85, 33, 125, 0.15);
        }
        .btn-ghost:hover {
            border-color: var(--orange);
            color: var(--orange);
            transform: translateY(-3px);
            box-shadow: var(--shadow-sm);
        }

        .btn-outline-light {
            background: transparent;
            color: #fff;
            border-color: rgba(255, 255, 255, 0.3);
        }
        .btn-outline-light:hover {
            background: rgba(255, 255, 255, 0.15);
            border-color: #fff;
            transform: translateY(-3px);
        }

        /* -------- Header / Nav -------- */
        .site-header {
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
            background: rgba(252, 246, 242, 0.85);
            border-bottom: 1px solid rgba(85, 33, 125, 0.06);
            transition: background var(--transition);
        }
        .site-header.scrolled {
            background: rgba(252, 246, 242, 0.95);
            box-shadow: 0 4px 20px rgba(39, 18, 65, 0.06);
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 80px;
            gap: 20px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 900;
        }
        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--orange), #ff934f 50%, var(--purple));
            display: grid;
            place-items: center;
            color: #fff;
            box-shadow: 0 12px 24px rgba(255, 107, 22, 0.25);
            transition: transform var(--transition);
        }
        .brand:hover .brand-mark {
            transform: rotate(-8deg) scale(1.05);
        }
        .brand-mark svg {
            width: 26px;
            height: 26px;
        }
        .brand-text strong {
            display: block;
            font-size: 1.25rem;
            line-height: 1.2;
            color: var(--purple);
        }
        .brand-text span {
            display: block;
            font-size: 0.65rem;
            color: var(--muted);
            letter-spacing: 0.1em;
            margin-top: 2px;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 28px;
            font-weight: 700;
            color: var(--muted);
        }
        .nav-links a {
            position: relative;
            padding: 4px 0;
        }
        .nav-links a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            right: 0;
            width: 0;
            height: 2.5px;
            background: var(--orange);
            transition: width var(--transition);
        }
        .nav-links a:hover {
            color: var(--purple);
        }
        .nav-links a:hover::after {
            width: 100%;
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .nav-actions .btn {
            padding: 10px 20px;
            font-size: 0.85rem;
        }

        /* Mobile menu toggle */
        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 8px;
        }
        .menu-toggle span {
            display: block;
            width: 26px;
            height: 3px;
            background: var(--purple);
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        .menu-toggle.active span:nth-child(1) {
            transform: rotate(45deg) translate(5px, 5px);
        }
        .menu-toggle.active span:nth-child(2) {
            opacity: 0;
        }
        .menu-toggle.active span:nth-child(3) {
            transform: rotate(-45deg) translate(5px, -5px);
        }

        /* Mobile nav overlay */
        .mobile-nav {
            display: none;
            position: fixed;
            top: 0;
            right: 0;
            width: 300px;
            height: 100%;
            background: var(--white);
            box-shadow: var(--shadow-lg);
            padding: 80px 30px 30px;
            z-index: 200;
            transform: translateX(100%);
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
        }
        .mobile-nav.open {
            transform: translateX(0);
        }
        .mobile-nav a {
            display: block;
            padding: 16px 0;
            border-bottom: 1px solid rgba(85, 33, 125, 0.06);
            font-weight: 700;
            color: var(--ink);
        }
        .mobile-nav a:hover {
            color: var(--orange);
        }
        .mobile-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 150;
            backdrop-filter: blur(4px);
        }
        .mobile-overlay.active {
            display: block;
        }

        /* -------- Hero Section -------- */
        .hero {
            padding: 60px 0 40px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(255, 107, 22, 0.08), transparent 70%);
            top: -200px;
            left: -200px;
            z-index: 0;
            pointer-events: none;
        }
        .hero::after {
            content: '';
            position: absolute;
            width: 500px;
            height: 500px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(85, 33, 125, 0.06), transparent 70%);
            bottom: -150px;
            right: -150px;
            z-index: 0;
            pointer-events: none;
        }
        .hero-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
            position: relative;
            z-index: 1;
        }
        .hero-content h1 {
            font-size: clamp(2.6rem, 6vw, 4.8rem);
            font-weight: 900;
            color: var(--purple);
            line-height: 1.08;
            letter-spacing: -0.02em;
            margin: 16px 0 20px;
        }
        .hero-content h1 span {
            color: var(--orange);
            position: relative;
        }
        .hero-content h1 span::after {
            content: '';
            position: absolute;
            bottom: 2px;
            left: 0;
            width: 100%;
            height: 6px;
            background: var(--orange);
            opacity: 0.25;
            border-radius: 4px;
        }
        .hero-content p {
            color: var(--muted);
            font-size: 1.15rem;
            line-height: 1.9;
            max-width: 600px;
        }
        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            margin-top: 32px;
        }
        .hero-metrics {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-top: 44px;
        }
        .metric {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(6px);
            border: 1px solid rgba(85, 33, 125, 0.08);
            border-radius: var(--radius-md);
            padding: 20px 16px;
            text-align: center;
            transition: all var(--transition);
        }
        .metric:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-md);
            border-color: rgba(255, 107, 22, 0.2);
        }
        .metric strong {
            display: block;
            font-size: 2rem;
            color: var(--purple);
            font-weight: 900;
            line-height: 1.2;
        }
        .metric span {
            font-weight: 700;
            color: var(--muted);
            font-size: 0.85rem;
        }

        /* Dashboard preview */
        .dashboard-preview {
            position: relative;
            min-height: 520px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.5;
            pointer-events: none;
        }
        .orb.one {
            width: 240px;
            height: 240px;
            background: rgba(255, 107, 22, 0.2);
            top: 0;
            right: 10%;
        }
        .orb.two {
            width: 280px;
            height: 280px;
            background: rgba(85, 33, 125, 0.15);
            bottom: 0;
            left: 5%;
        }
        .phone-card {
            width: 320px;
            min-height: 540px;
            background: var(--white);
            border-radius: 44px;
            padding: 16px;
            box-shadow: var(--shadow-lg);
            border: 1px solid rgba(85, 33, 125, 0.08);
            transform: rotate(-4deg) translateY(-10px);
            transition: transform 0.5s ease;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
        }
        .phone-card:hover {
            transform: rotate(0deg) translateY(-16px);
        }
        .phone-screen {
            background: linear-gradient(180deg, #fff, #fcf6f2);
            border-radius: 32px;
            overflow: hidden;
            border: 1px solid rgba(255, 107, 22, 0.08);
            min-height: 508px;
        }
        .phone-top {
            background: linear-gradient(135deg, var(--purple), var(--purple-dark));
            padding: 24px 20px 20px;
            color: #fff;
        }
        .phone-top strong {
            font-size: 1.2rem;
        }
        .search-bar {
            height: 44px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(4px);
            border-radius: 999px;
            display: flex;
            align-items: center;
            padding: 0 16px;
            margin-top: 14px;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .category-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            padding: 18px 16px;
        }
        .mini-icon {
            background: var(--white);
            border-radius: var(--radius-sm);
            height: 64px;
            display: grid;
            place-items: center;
            font-size: 1.6rem;
            border: 1px solid rgba(255, 107, 22, 0.1);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition);
        }
        .mini-icon:hover {
            transform: scale(1.08);
            border-color: var(--orange);
        }
        .shipment-card {
            margin: 8px 16px 14px;
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 16px;
            border: 1px solid rgba(85, 33, 125, 0.06);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition);
        }
        .shipment-card:hover {
            transform: translateX(-6px);
            border-color: var(--orange);
        }
        .shipment-card small {
            color: var(--muted);
            font-size: 0.75rem;
        }
        .shipment-card strong {
            display: block;
            color: var(--purple);
            margin-top: 4px;
            font-size: 1rem;
        }

        .floating-card {
            position: absolute;
            right: -20px;
            bottom: 40px;
            width: 240px;
            padding: 24px;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(20px);
            border-radius: var(--radius-md);
            border: 1px solid rgba(85, 33, 125, 0.06);
            box-shadow: var(--shadow-lg);
            z-index: 3;
            transition: all var(--transition);
        }
        .floating-card:hover {
            transform: translateY(-8px) scale(1.02);
        }
        .floating-card strong {
            color: var(--purple);
            font-size: 1.1rem;
            display: block;
            margin-bottom: 8px;
        }
        .progress {
            height: 8px;
            background: #f0e8f5;
            border-radius: 999px;
            overflow: hidden;
            margin: 14px 0 10px;
        }
        .progress span {
            display: block;
            height: 100%;
            width: 72%;
            background: linear-gradient(90deg, var(--orange), var(--purple));
            border-radius: 999px;
        }

        /* -------- Generic Sections -------- */
        .section {
            padding: 80px 0;
        }
        .section-head {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 30px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 24px;
        }
        .card {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 32px 28px;
            border: 1px solid rgba(85, 33, 125, 0.06);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition);
            position: relative;
            overflow: hidden;
        }
        .card::before {
            content: '';
            position: absolute;
            inset: 0 0 auto 0;
            height: 4px;
            background: linear-gradient(90deg, var(--orange), var(--purple));
            opacity: 0;
            transition: opacity var(--transition);
        }
        .card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-md);
            border-color: rgba(255, 107, 22, 0.15);
        }
        .card:hover::before {
            opacity: 1;
        }
        .card-icon {
            width: 56px;
            height: 56px;
            background: rgba(255, 107, 22, 0.08);
            border-radius: var(--radius-sm);
            display: grid;
            place-items: center;
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: var(--orange);
            transition: all var(--transition);
        }
        .card:hover .card-icon {
            background: var(--orange);
            color: #fff;
            transform: scale(1.05) rotate(-6deg);
        }
        .card h3 {
            font-size: 1.4rem;
            color: var(--purple);
            margin-bottom: 12px;
        }
        .card p {
            color: var(--muted);
            line-height: 1.8;
        }

        /* -------- Split Layout (About) -------- */
        .split {
            display: grid;
            grid-template-columns: 1fr 1.1fr;
            gap: 40px;
            align-items: center;
        }
        .content-card {
            background: var(--white);
            border-radius: var(--radius-md);
            padding: 40px;
            box-shadow: var(--shadow-sm);
            border: 1px solid rgba(85, 33, 125, 0.06);
        }
        .content-card h2 {
            color: var(--purple);
            font-size: 2.2rem;
            margin-bottom: 16px;
        }
        .content-card p {
            color: var(--muted);
            line-height: 2;
            margin-bottom: 14px;
        }
        .visual-panel {
            background: linear-gradient(135deg, var(--purple), #2e1045);
            border-radius: var(--radius-md);
            padding: 36px 32px;
            color: #fff;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }
        .visual-panel::after {
            content: '';
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 107, 22, 0.2);
            bottom: -100px;
            left: -80px;
            pointer-events: none;
        }
        .visual-panel h2 {
            color: #fff;
            margin-bottom: 24px;
            position: relative;
            z-index: 2;
        }
        .timeline {
            display: grid;
            gap: 16px;
            position: relative;
            z-index: 2;
        }
        .timeline-item {
            display: flex;
            gap: 16px;
            align-items: flex-start;
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: var(--radius-sm);
            padding: 18px 20px;
            transition: all var(--transition);
        }
        .timeline-item:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(-6px);
        }
        .timeline-number {
            width: 36px;
            height: 36px;
            border-radius: 12px;
            background: var(--orange);
            display: grid;
            place-items: center;
            font-weight: 900;
            flex-shrink: 0;
        }
        .timeline-item strong {
            font-size: 1.1rem;
        }
        .timeline-item p {
            color: rgba(255, 255, 255, 0.8);
            margin: 4px 0 0;
            font-size: 0.9rem;
            line-height: 1.6;
        }

        /* -------- Apps Section -------- */
        .apps {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }
        .app-card {
            border-radius: var(--radius-md);
            padding: 32px 28px;
            color: #fff;
            min-height: 260px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-md);
            transition: all var(--transition);
        }
        .app-card:hover {
            transform: translateY(-10px) scale(1.01);
            box-shadow: var(--shadow-lg);
        }
        .app-card.metwzon {
            background: linear-gradient(145deg, #ff6b16, #b84a0f);
        }
        .app-card.express {
            background: linear-gradient(145deg, #55217d, #7c37b4);
        }
        .app-card.go {
            background: linear-gradient(145deg, #1f0f30, #ff6b16);
        }
        .app-card::after {
            content: '';
            position: absolute;
            width: 200px;
            height: 200px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            left: -60px;
            bottom: -60px;
            pointer-events: none;
        }
        .app-card h3 {
            font-size: 1.8rem;
            margin-bottom: 10px;
            position: relative;
            z-index: 2;
        }
        .app-card p {
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.8;
            position: relative;
            z-index: 2;
            max-width: 90%;
        }
        .store-row {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 24px;
            position: relative;
            z-index: 2;
        }
        .store-badge {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(4px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 8px 16px;
            font-size: 0.8rem;
            font-weight: 700;
        }

        /* -------- Media Grid -------- */
        .media-grid {
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            gap: 30px;
        }
        .promo-image,
        .promo-video {
            border-radius: var(--radius-md);
            padding: 32px 28px;
            background: var(--white);
            border: 1px solid rgba(85, 33, 125, 0.06);
            box-shadow: var(--shadow-sm);
            transition: all var(--transition);
        }
        .promo-image:hover,
        .promo-video:hover {
            box-shadow: var(--shadow-md);
        }
        .promo-image {
            background: linear-gradient(135deg, rgba(255, 107, 22, 0.05), rgba(85, 33, 125, 0.05)), var(--white);
        }
        .promo-image h3,
        .promo-video h3 {
            color: var(--purple);
            font-size: 1.6rem;
            margin-bottom: 10px;
        }
        .promo-image p,
        .promo-video p {
            color: var(--muted);
            line-height: 1.8;
        }
        .promo-video {
            background: linear-gradient(135deg, #1f0f30, #55217d);
            color: #fff;
        }
        .promo-video h3 {
            color: #fff;
        }
        .promo-video p {
            color: rgba(255, 255, 255, 0.8);
        }
        .play {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: var(--orange);
            display: grid;
            place-items: center;
            font-size: 2rem;
            color: #fff;
            box-shadow: 0 20px 40px rgba(255, 107, 22, 0.3);
            margin-top: 28px;
            transition: all var(--transition);
        }
        .play:hover {
            transform: scale(1.1);
            box-shadow: 0 28px 56px rgba(255, 107, 22, 0.4);
        }

        /* -------- Login Strip -------- */
        .login-strip {
            background: linear-gradient(135deg, var(--purple), #2e1045);
            color: #fff;
            border-radius: var(--radius-md);
            padding: 40px 48px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            flex-wrap: wrap;
            box-shadow: var(--shadow-lg);
        }
        .login-strip h2 {
            color: #fff;
            font-size: 2rem;
            margin-bottom: 6px;
        }
        .login-strip p {
            color: rgba(255, 255, 255, 0.8);
            margin: 0;
        }
        .login-links {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }
        .login-links .btn {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(4px);
            border-color: rgba(255, 255, 255, 0.15);
            color: #fff;
        }
        .login-links .btn:hover {
            background: var(--orange);
            border-color: var(--orange);
            transform: translateY(-3px);
        }

        /* -------- Footer -------- */
        .footer {
            padding: 48px 0 32px;
            border-top: 1px solid rgba(85, 33, 125, 0.06);
            color: var(--muted);
        }
        .footer-grid {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            flex-wrap: wrap;
        }
        .footer-links {
            display: flex;
            gap: 24px;
            flex-wrap: wrap;
            font-weight: 700;
        }
        .footer-links a:hover {
            color: var(--orange);
        }

        /* -------- Responsive -------- */
        @media (max-width: 1024px) {
            .hero-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }
            .dashboard-preview {
                min-height: 400px;
            }
            .phone-card {
                width: 280px;
                min-height: 460px;
            }
            .phone-screen {
                min-height: 428px;
            }
            .floating-card {
                position: relative;
                right: auto;
                bottom: auto;
                width: 100%;
                margin-top: 20px;
            }
            .split {
                grid-template-columns: 1fr;
            }
            .media-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }
            .menu-toggle {
                display: flex;
            }
            .mobile-nav,
            .mobile-overlay.active {
                display: block;
            }

            .nav-actions .btn {
                padding: 8px 14px;
                font-size: 0.75rem;
            }
            .nav-actions .btn:not(.btn-primary) {
                display: none;
            }

            .hero {
                padding: 40px 0 20px;
            }
            .hero-metrics {
                grid-template-columns: 1fr 1fr;
            }
            .metric strong {
                font-size: 1.6rem;
            }

            .cards {
                grid-template-columns: 1fr;
            }
            .apps {
                grid-template-columns: 1fr;
            }
            .section-head {
                flex-direction: column;
                align-items: flex-start;
            }
            .login-strip {
                flex-direction: column;
                align-items: flex-start;
                padding: 28px 24px;
            }
            .footer-grid {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: calc(100% - 20px);
            }
            .phone-card {
                width: 100%;
                transform: none;
            }
            .hero-content h1 {
                font-size: 2.2rem;
            }
            .btn {
                padding: 10px 18px;
                font-size: 0.85rem;
            }
            .brand-text strong {
                font-size: 1rem;
            }
        }

        /* -------- Utility Animations -------- */
        .fade-up {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .fade-up.visible {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body>
    <header class="site-header" id="siteHeader">
        <div class="container nav">
            <a href="{{ route('website.home') }}" class="brand" aria-label="MetwLogistic Home">
                <span class="brand-mark">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 8.5 12 4l8 4.5v7L12 20l-8-4.5v-7Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m4 8.5 8 4.5 8-4.5M12 13v7" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                </span>
                <span class="brand-text">
                    <strong>ميتولوجيستيك</strong>
                    <span>MetwLogistic</span>
                </span>
            </a>

            <nav class="nav-links" aria-label="Main navigation">
                <a href="{{ route('website.home') }}#services">الخدمات</a>
                <a href="{{ route('website.about') }}">نحن</a>
                <a href="{{ route('website.home') }}#apps">التطبيقات</a>
                <a href="{{ route('website.home') }}#media">المحتوى الدعائي</a>
                <a href="{{ route('website.policies') }}">السياسات</a>
            </nav>

            <div class="nav-actions">
                <a class="btn btn-ghost" href="{{ route('vendor.login') }}">دخول البائع</a>
                <a class="btn btn-ghost" href="{{ route('shipment.login') }}">دخول الشحن</a>
                <a class="btn btn-primary" href="{{ route('admin.login') }}">دخول الأدمن</a>
                <button class="menu-toggle" id="menuToggle" aria-label="فتح القائمة">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Navigation -->
    <div class="mobile-overlay" id="mobileOverlay"></div>
    <nav class="mobile-nav" id="mobileNav" aria-label="Mobile navigation">
        <a href="{{ route('website.home') }}#services">الخدمات</a>
        <a href="{{ route('website.about') }}">نحن</a>
        <a href="{{ route('website.home') }}#apps">التطبيقات</a>
        <a href="{{ route('website.home') }}#media">المحتوى الدعائي</a>
        <a href="{{ route('website.policies') }}">السياسات</a>
        <a href="{{ route('vendor.login') }}">دخول البائع</a>
        <a href="{{ route('shipment.login') }}">دخول الشحن</a>
        <a href="{{ route('admin.login') }}" style="color:var(--orange);">دخول الأدمن</a>
    </nav>

    <main>
        @yield('content')
    </main>

    <footer class="footer">
        <div class="container footer-grid">
            <div class="brand">
                <span class="brand-mark">
                    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M4 8.5 12 4l8 4.5v7L12 20l-8-4.5v-7Z" stroke="currentColor" stroke-width="1.8"/>
                        <path d="m4 8.5 8 4.5 8-4.5M12 13v7" stroke="currentColor" stroke-width="1.8"/>
                    </svg>
                </span>
                <span class="brand-text">
                    <strong>ميتولوجيستيك</strong>
                    <span>MetwLogistic</span>
                </span>
            </div>
            <div class="footer-links">
                <a href="{{ route('website.about') }}">نحن</a>
                <a href="{{ route('website.policies') }}">السياسات</a>
                <a href="{{ route('vendor.register') }}">تسجيل بائع</a>
                <a href="{{ route('shipment.register') }}">تسجيل شحن</a>
            </div>
            <div>&copy; {{ date('Y') }} MetwLogistic. جميع الحقوق محفوظة.</div>
        </div>
    </footer>

    <script>
        (function() {
            'use strict';

            // -------- Mobile Menu --------
            const toggle = document.getElementById('menuToggle');
            const mobileNav = document.getElementById('mobileNav');
            const overlay = document.getElementById('mobileOverlay');

            function openMenu() {
                mobileNav.classList.add('open');
                overlay.classList.add('active');
                toggle.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
            function closeMenu() {
                mobileNav.classList.remove('open');
                overlay.classList.remove('active');
                toggle.classList.remove('active');
                document.body.style.overflow = '';
            }

            toggle.addEventListener('click', () => {
                if (mobileNav.classList.contains('open')) {
                    closeMenu();
                } else {
                    openMenu();
                }
            });

            overlay.addEventListener('click', closeMenu);

            // Close on escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && mobileNav.classList.contains('open')) {
                    closeMenu();
                }
            });

            // -------- Scroll Header Shadow --------
            const header = document.getElementById('siteHeader');
            let lastScroll = 0;
            window.addEventListener('scroll', () => {
                const scrollY = window.pageYOffset || document.documentElement.scrollTop;
                if (scrollY > 20) {
                    header.classList.add('scrolled');
                } else {
                    header.classList.remove('scrolled');
                }
                lastScroll = scrollY;
            });

            // -------- Intersection Observer for fade-up --------
            const fadeElements = document.querySelectorAll('.fade-up');
            if ('IntersectionObserver' in window) {
                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                        }
                    });
                }, {
                    threshold: 0.1,
                    rootMargin: '0px 0px -40px 0px'
                });
                fadeElements.forEach(el => observer.observe(el));
            } else {
                // Fallback: show all immediately
                fadeElements.forEach(el => el.classList.add('visible'));
            }

        })();
    </script>
</body>
</html>
<?php
/**
 * Front-end template for [sleep_apnea_form] shortcode.
 * Variables in scope: $s (settings), $nonce, $ajax_url, $redirect
 */
if ( ! defined( 'ABSPATH' ) ) exit;
$brand   = $s['brand_name']   ?? 'Riverwalk Dentistry';
$city    = $s['brand_city']   ?? 'Waterloo';
$booking   = $s['result_book_url']      ?: ( $s['success_redirect_url'] ?? '' );
$ins_url   = $s['result_insurance_url'] ?? '';
$brand_url = ! empty( $s['brand_url'] ) ? $s['brand_url'] : home_url( '/' );
?>
<div class="sapn-app bg-background min-h-screen text-foreground" data-sapn-redirect="<?= esc_attr( $redirect ) ?>">
  <style>
    /* ── Inherit the host site's design tokens.
       The site defines colors as HSL triplets on :root (e.g. --primary: 155 30% 30%)
       and uses them as hsl(var(--primary)). We bridge those triplets into our own
       scoped vars via a non-circular trick: copy the site's triplet to a private
       --__sapn_* var on :root (no cycle there), then redefine the form's color
       tokens inside .sapn-app to be hsl() of that bridge var. If the site doesn't
       define a token, the HSL triplet fallback inside var(..., FALLBACK) is used —
       so the form still looks like a sage-green sleep-medicine form on a vanilla
       theme. ── */
    :root{
      --__sapn_background:           var(--background, 40 30% 98%);
      --__sapn_foreground:           var(--foreground, 160 10% 25%);
      --__sapn_card:                 var(--card, 60 20% 99%);
      --__sapn_card_fg:              var(--card-foreground, 160 10% 25%);
      --__sapn_popover:              var(--popover, 60 20% 99%);
      --__sapn_popover_fg:           var(--popover-foreground, 160 10% 25%);
      --__sapn_primary:              var(--primary, 155 30% 30%);
      --__sapn_primary_fg:           var(--primary-foreground, 60 30% 98%);
      --__sapn_secondary:            var(--secondary, 130 15% 95%);
      --__sapn_secondary_fg:         var(--secondary-foreground, 160 15% 30%);
      --__sapn_muted:                var(--muted, 110 10% 95%);
      --__sapn_muted_fg:             var(--muted-foreground, 150 5% 50%);
      --__sapn_accent:               var(--accent, 145 25% 88%);
      --__sapn_accent_fg:            var(--accent-foreground, 160 15% 28%);
      --__sapn_border:               var(--border, 130 10% 90%);
      --__sapn_input:                var(--input, 120 10% 92%);
      --__sapn_ring:                 var(--ring, 155 30% 30%);
      --__sapn_destructive:          var(--destructive, 0 75% 55%);
      --__sapn_destructive_fg:       var(--destructive-foreground, 0 0% 98%);
    }
    .sapn-app{
      --radius: 1rem;
      --background:          hsl(var(--__sapn_background));
      --foreground:          hsl(var(--__sapn_foreground));
      --card:                hsl(var(--__sapn_card));
      --card-foreground:     hsl(var(--__sapn_card_fg));
      --popover:             hsl(var(--__sapn_popover));
      --popover-foreground:  hsl(var(--__sapn_popover_fg));
      --primary:             hsl(var(--__sapn_primary));
      --primary-foreground:  hsl(var(--__sapn_primary_fg));
      --secondary:           hsl(var(--__sapn_secondary));
      --secondary-foreground:hsl(var(--__sapn_secondary_fg));
      --muted:               hsl(var(--__sapn_muted));
      --muted-foreground:    hsl(var(--__sapn_muted_fg));
      --accent:              hsl(var(--__sapn_accent));
      --accent-foreground:   hsl(var(--__sapn_accent_fg));
      --border:              hsl(var(--__sapn_border));
      --input:               hsl(var(--__sapn_input));
      --ring:                hsl(var(--__sapn_ring));
      --destructive:         hsl(var(--__sapn_destructive));
      --destructive-foreground: hsl(var(--__sapn_destructive_fg));
      --shadow-soft: 0 8px 24px -12px hsla(var(--__sapn_primary), 0.18);
      --shadow-card: 0 2px 12px -4px  hsla(var(--__sapn_primary), 0.10);
      background-color: var(--background);
      color: var(--foreground);
    }
    /* ── Beat the WP theme on the handful of color utilities the form relies on ── */
    .sapn-app .text-primary{color:var(--primary)!important;}
    .sapn-app .text-primary-foreground{color:var(--primary-foreground)!important;}
    .sapn-app .text-foreground{color:var(--foreground)!important;}
    .sapn-app .text-muted-foreground{color:var(--muted-foreground)!important;}
    .sapn-app .text-red-600{color:#dc2626!important;}
    .sapn-app .bg-background{background-color:var(--background)!important;}
    .sapn-app .bg-card{background-color:var(--card)!important;}
    .sapn-app .bg-card\/60{background-color:color-mix(in oklab,var(--card) 60%,transparent)!important;}
    .sapn-app .bg-primary{background-color:var(--primary)!important;}
    .sapn-app .bg-primary\/15{background-color:color-mix(in oklab,var(--primary) 15%,transparent)!important;}
    .sapn-app .bg-primary\/20{background-color:color-mix(in oklab,var(--primary) 20%,transparent)!important;}
    .sapn-app .bg-primary\/95{background-color:color-mix(in oklab,var(--primary) 95%,transparent)!important;}
    .sapn-app .bg-secondary{background-color:var(--secondary)!important;}
    .sapn-app .bg-secondary\/40{background-color:color-mix(in oklab,var(--secondary) 40%,transparent)!important;}
    .sapn-app .bg-accent\/20{background-color:color-mix(in oklab,var(--accent) 20%,transparent)!important;}
    .sapn-app .bg-accent\/30{background-color:color-mix(in oklab,var(--accent) 30%,transparent)!important;}
    .sapn-app .bg-accent\/50{background-color:color-mix(in oklab,var(--accent) 50%,transparent)!important;}
    .sapn-app .bg-accent\/60{background-color:color-mix(in oklab,var(--accent) 60%,transparent)!important;}
    .sapn-app .bg-background\/60{background-color:color-mix(in oklab,var(--background) 60%,transparent)!important;}
    .sapn-app .bg-background\/80{background-color:color-mix(in oklab,var(--background) 80%,transparent)!important;}
    .sapn-app .border-border{border-color:var(--border)!important;}
    .sapn-app .border-border\/60{border-color:color-mix(in oklab,var(--border) 60%,transparent)!important;}
    .sapn-app .border-primary{border-color:var(--primary)!important;}
    .sapn-app .border-primary\/20{border-color:color-mix(in oklab,var(--primary) 20%,transparent)!important;}
    .sapn-app .border-input{border-color:var(--input)!important;}
    .sapn-app .hover\:border-primary\/40:hover{border-color:color-mix(in oklab,var(--primary) 40%,transparent)!important;}
    .sapn-app .focus\:border-primary:focus{border-color:var(--primary)!important;}
    .sapn-app .divide-border\/60 > * + *{border-color:color-mix(in oklab,var(--border) 60%,transparent)!important;}
    /* SVGs use stroke="currentColor" — make sure nothing forces a different stroke */
    .sapn-app svg{stroke:currentColor;color:currentColor;}
    .sapn-app .text-primary svg,.sapn-app svg.text-primary{color:var(--primary)!important;stroke:var(--primary)!important;}

    .sapn-app .rw-loader-wrap{display:flex;flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:4.5rem 0 5rem;}
    .sapn-app .rw-loader-mark{display:flex;align-items:center;justify-content:center;width:56px;height:56px;border-radius:999px;background:color-mix(in oklab,var(--accent) 60%,transparent);color:var(--primary);animation:rw-pulse 1.6s ease-in-out infinite;}
    @keyframes rw-pulse{0%,100%{transform:scale(1);opacity:1}50%{transform:scale(.94);opacity:.78}}
    .sapn-app .rw-loader-text{margin-top:1.25rem;font-size:.875rem;color:var(--muted-foreground);}
    .sapn-app .rw-loader-track{margin-top:.875rem;width:96px;height:2px;border-radius:999px;background:color-mix(in oklab,var(--secondary) 80%,transparent);overflow:hidden;position:relative;}
    .sapn-app .rw-loader-track::before{content:'';position:absolute;top:0;left:0;height:100%;width:40%;border-radius:999px;background:color-mix(in oklab,var(--muted-foreground) 70%,transparent);animation:rw-loader 1.1s ease-in-out infinite;}
    @keyframes rw-loader{0%{transform:translateX(-110%)}100%{transform:translateX(260%)}}
    .sapn-app .opt-selected{background-color:color-mix(in oklab,var(--accent) 50%,transparent)!important;border-color:var(--primary)!important;box-shadow:var(--shadow-card);}
    .sapn-app .opt-selected .opt-icon-wrap{background-color:var(--primary)!important;color:var(--primary-foreground)!important;}
    .sapn-app .opt-selected .opt-radio{background-color:var(--primary)!important;border-color:var(--primary)!important;color:var(--primary-foreground)!important;}
    .sapn-app .opt-selected .opt-check svg{display:block!important;}
    .sapn-app .opt-check svg{display:none;}
    .sapn-app .pill-active{background-color:color-mix(in oklab,var(--primary) 15%,transparent)!important;color:var(--foreground)!important;border-color:var(--primary)!important;}
    .sapn-app .view-fade{animation:rw-fade 280ms ease both;}
    @keyframes rw-fade{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
    .sapn-app [data-view]{transition:opacity 320ms ease, transform 320ms ease;}
    .sapn-app [data-view].view-leaving{opacity:0;transform:translateY(-6px);pointer-events:none;}
    .sapn-app [data-view].view-entering{opacity:0;transform:translateY(10px);}

    /* Scoped Tailwind preflight bits we still need (without leaking to the page):
       - border-style: solid so divide-y / border-t / border draws (otherwise 'border-width: 1px'
         on a default border-style:none element renders nothing — the divider lines vanish).
       - box-sizing: border-box so widths/paddings behave the way the layout expects. */
    .sapn-app, .sapn-app *, .sapn-app ::before, .sapn-app ::after {
      box-sizing: border-box;
      border-style: solid;
      border-width: 0;
    }
    /* The compiled Tailwind wraps several border utilities in :where(...), giving them 0,0,0,0
       specificity — my .sapn-app * { border-width: 0 } reset (0,0,1,1) beats them and dividers
       vanish. Re-state the actual border-width values inside .sapn-app with high specificity. */
    .sapn-app .divide-y > :not(:last-child)        { border-bottom-width: 1px !important; }
    .sapn-app .divide-y-reverse > :not(:first-child){ border-top-width: 1px !important; }
    .sapn-app .border    { border-width: 1px !important; }
    .sapn-app .border-2  { border-width: 2px !important; }
    .sapn-app .border-t  { border-top-width: 1px !important; }
    .sapn-app .border-b  { border-bottom-width: 1px !important; }
    .sapn-app .border-l  { border-left-width: 1px !important; }
    .sapn-app .border-r  { border-right-width: 1px !important; }
    /* Neutralise the theme's .entry-content paragraph spacing without killing Tailwind mt-* / mb-*
       utilities. We zero margins on the base elements (no !important, so utility classes still
       win), then re-declare the actual mt-* / mb-* values below with !important to beat themes
       that DO use !important. Padding is NOT reset on li — Tailwind's px-* / py-* handle that. */
    .sapn-app p,.sapn-app h1,.sapn-app h2,.sapn-app h3,.sapn-app h4,.sapn-app h5,.sapn-app h6{margin:0;padding:0;}
    .sapn-app ul,.sapn-app ol{margin:0;padding-left:0;list-style:none;}
    .sapn-app li{margin:0;}
    .sapn-app .mt-1{margin-top:.25rem!important;} .sapn-app .mt-1\.5{margin-top:.375rem!important;}
    .sapn-app .mt-2{margin-top:.5rem!important;} .sapn-app .mt-2\.5{margin-top:.625rem!important;}
    .sapn-app .mt-3{margin-top:.75rem!important;} .sapn-app .mt-4{margin-top:1rem!important;}
    .sapn-app .mt-5{margin-top:1.25rem!important;} .sapn-app .mt-6{margin-top:1.5rem!important;}
    .sapn-app .mt-8{margin-top:2rem!important;} .sapn-app .mt-12{margin-top:3rem!important;}
    .sapn-app .mt-16{margin-top:4rem!important;} .sapn-app .mt-20{margin-top:5rem!important;}
    .sapn-app .mb-1{margin-bottom:.25rem!important;} .sapn-app .mb-1\.5{margin-bottom:.375rem!important;}
    .sapn-app .mb-4{margin-bottom:1rem!important;} .sapn-app .mb-6{margin-bottom:1.5rem!important;}
    .sapn-app .my-1{margin-top:.25rem!important;margin-bottom:.25rem!important;}

    /* Smooth selection + step transitions */
    .sapn-app .opt-selected{transition:background-color 200ms ease,border-color 200ms ease,box-shadow 200ms ease;}
    .sapn-app .opt-selected .opt-radio{animation:rw-pop 340ms cubic-bezier(.34,1.56,.64,1) both;}
    .sapn-app .opt-selected .opt-icon-wrap{transition:background-color 200ms ease,color 200ms ease;}
    .sapn-app .opt-selected .opt-check svg{animation:rw-check-in 320ms cubic-bezier(.34,1.56,.64,1) both;}
    @keyframes rw-pop{0%{transform:scale(.4);opacity:0;}60%{transform:scale(1.15);opacity:1;}100%{transform:scale(1);opacity:1;}}
    @keyframes rw-check-in{0%{transform:scale(0);opacity:0;}60%{transform:scale(1.2);opacity:1;}100%{transform:scale(1);opacity:1;}}

    .sapn-app .step-leaving{animation:rw-step-out 220ms ease both;}
    .sapn-app .step-entering{animation:rw-step-in 320ms cubic-bezier(.2,.65,.3,1) both;}
    @keyframes rw-step-out{from{opacity:1;transform:translateY(0);filter:blur(0);}to{opacity:0;transform:translateY(-6px);filter:blur(2px);}}
    @keyframes rw-step-in{from{opacity:0;transform:translateY(10px);filter:blur(2px);}to{opacity:1;transform:translateY(0);filter:blur(0);}}

    /* Tailwind arbitrary-value & gradient utilities that may have been
       dropped from the JIT compile — force them on the locked estimate card. */
    .sapn-app .blur-\[6px\]{filter:blur(6px)!important;}
    .sapn-app .select-none{user-select:none!important;}
    .sapn-app .bg-gradient-to-b.from-background\/40.to-background\/80{
      background-image:linear-gradient(to bottom,
        color-mix(in oklab,var(--background) 40%,transparent),
        color-mix(in oklab,var(--background) 80%,transparent))!important;
    }
    .sapn-app .pointer-events-none{pointer-events:none!important;}
  </style>

  <!-- LANDING -->
  <section data-view="landing">
    <main class="pb-28 sm:pb-16">
      <section class="mx-auto px-5 pt-10 sm:pt-20 max-w-3xl text-center">
        <span class="inline-flex items-center gap-1.5 bg-card px-3 py-1 border border-border rounded-full text-muted-foreground text-xs">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 text-primary"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path></svg>
          <?= esc_html( $s['hero_eyebrow'] ) ?>
        </span>
        <h1 class="mt-5 font-semibold text-4xl sm:text-5xl leading-tight tracking-tight"><?= esc_html( $s['hero_heading'] ) ?></h1>
        <p class="mt-5 text-muted-foreground text-base sm:text-lg leading-relaxed"><?= esc_html( $s['hero_subheading'] ) ?></p>
        <button type="button" data-action="go-intro" class="inline-flex justify-center items-center gap-2 bg-primary hover:opacity-95 shadow-[var(--shadow-soft)] mt-8 px-8 py-4 rounded-full font-medium text-primary-foreground text-base transition">
          <?= esc_html( $s['hero_cta'] ) ?>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
        </button>
        <p class="mt-3 text-muted-foreground text-xs">Custom sleep appliances, snoring support, and airway-focused care in <?= esc_html( $city ) ?></p>
      </section>

      <!-- Trust cards -->
      <section class="gap-4 grid sm:grid-cols-3 mx-auto mt-16 px-5 max-w-4xl">
        <div class="bg-card shadow-[var(--shadow-card)] p-5 border border-border rounded-2xl">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-primary"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
          <p class="mt-3 font-medium text-foreground">Not a diagnosis</p>
          <p class="mt-1 text-muted-foreground text-sm leading-relaxed">Our estimator is educational. Final recommendations come after a clinical evaluation.</p>
        </div>
        <div class="bg-card shadow-[var(--shadow-card)] p-5 border border-border rounded-2xl">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-primary"><path d="M16 20V4a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/><rect width="20" height="14" x="2" y="6" rx="2"/></svg>
          <p class="mt-3 font-medium text-foreground">Travel-friendly</p>
          <p class="mt-1 text-muted-foreground text-sm leading-relaxed">Custom appliances are quiet, compact, and easy to take with you.</p>
        </div>
        <div class="bg-card shadow-[var(--shadow-card)] p-5 border border-border rounded-2xl">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-primary"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/><path d="M3.22 13H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"/></svg>
          <p class="mt-3 font-medium text-foreground">Patient-led care</p>
          <p class="mt-1 text-muted-foreground text-sm leading-relaxed">We meet you where you are — no pressure, just honest guidance.</p>
        </div>
      </section>

      <!-- What we help with -->
      <section class="mx-auto mt-20 px-5 max-w-4xl">
        <h2 class="font-semibold text-2xl sm:text-3xl text-center tracking-tight">What we help with</h2>
        <div class="gap-3 grid sm:grid-cols-2 mt-8">
          <div class="flex gap-4 bg-card p-5 border border-border rounded-2xl">
            <span class="flex flex-none justify-center items-center bg-accent/60 rounded-xl w-10 h-10 text-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"/><path d="M16 9a5 5 0 0 1 0 6"/><path d="M19.364 18.364a9 9 0 0 0 0-12.728"/></svg>
            </span>
            <div>
              <p class="font-medium">Snoring</p>
              <p class="mt-1 text-muted-foreground text-sm leading-relaxed">For you — and the partner trying to sleep next to you.</p>
            </div>
          </div>
          <div class="flex gap-4 bg-card p-5 border border-border rounded-2xl">
            <span class="flex flex-none justify-center items-center bg-accent/60 rounded-xl w-10 h-10 text-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M12.8 19.6A2 2 0 1 0 14 16H2"/><path d="M17.5 8a2.5 2.5 0 1 1 2 4H2"/><path d="M9.8 4.4A2 2 0 1 1 11 8H2"/></svg>
            </span>
            <div>
              <p class="font-medium">CPAP intolerance</p>
              <p class="mt-1 text-muted-foreground text-sm leading-relaxed">Quieter, smaller, custom-fit alternatives when CPAP isn't working.</p>
            </div>
          </div>
          <div class="flex gap-4 bg-card p-5 border border-border rounded-2xl">
            <span class="flex flex-none justify-center items-center bg-accent/60 rounded-xl w-10 h-10 text-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/></svg>
            </span>
            <div>
              <p class="font-medium">Sleep apnea</p>
              <p class="mt-1 text-muted-foreground text-sm leading-relaxed">Oral appliance therapy for appropriate mild and moderate cases.</p>
            </div>
          </div>
          <div class="flex gap-4 bg-card p-5 border border-border rounded-2xl">
            <span class="flex flex-none justify-center items-center bg-accent/60 rounded-xl w-10 h-10 text-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/></svg>
            </span>
            <div>
              <p class="font-medium">Airway-focused care</p>
              <p class="mt-1 text-muted-foreground text-sm leading-relaxed">Myofunctional support for breathing, posture, and oral habits.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- Curious where you'd start? -->
      <section class="mx-auto mt-20 px-5 max-w-2xl text-center">
        <div class="bg-card shadow-[var(--shadow-soft)] p-8 border border-primary/20 rounded-3xl">
          <h2 class="font-semibold text-2xl tracking-tight">Curious where you'd start?</h2>
          <p class="mt-3 text-muted-foreground text-sm leading-relaxed">Answer a few quick questions and we'll suggest a treatment path and estimated investment range. About 60 seconds.</p>
          <button type="button" data-action="go-intro" class="inline-flex justify-center items-center gap-2 bg-primary hover:opacity-95 mt-6 px-6 py-3.5 rounded-full font-medium text-primary-foreground text-sm">
            Start My Estimate
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>
          </button>
        </div>
      </section>
    </main>
  </section>

  <!-- INTRO -->
  <section data-view="intro" class="hidden">
    <header class="top-0 z-10 sticky bg-background/80 backdrop-blur border-border/60 border-b">
      <div class="flex justify-between items-center mx-auto px-5 py-4 max-w-2xl">
        <a href="<?= esc_url( $brand_url ) ?>" class="flex items-center gap-2 font-medium text-foreground text-sm no-underline">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-primary"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path></svg>
          <?= esc_html( $brand ) ?>
        </a>
      </div>
    </header>
    <main class="mx-auto px-5 py-8 sm:py-12 max-w-2xl">
      <div class="mt-6 sm:mt-12 text-center">
        <div class="flex justify-center items-center bg-accent/60 shadow-[var(--shadow-soft)] mx-auto mb-6 rounded-2xl w-16 h-16 text-primary">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-8 h-8"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"></path></svg>
        </div>
        <h1 class="font-semibold text-foreground text-3xl sm:text-4xl tracking-tight"><?= esc_html( $s['intro_heading'] ) ?></h1>
        <p class="mt-4 text-muted-foreground text-base leading-relaxed"><?= esc_html( $s['intro_sub'] ) ?></p>
        <button type="button" data-action="start-form" class="inline-flex justify-center items-center gap-2 bg-primary hover:opacity-95 shadow-[var(--shadow-soft)] mt-8 px-8 py-4 rounded-full w-full sm:w-auto font-medium text-primary-foreground text-base transition">
          <?= esc_html( $s['intro_cta'] ) ?>
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M5 12h14"></path><path d="m12 5 7 7-7 7"></path></svg>
        </button>
        <p class="mt-3 text-muted-foreground text-xs">Takes about 60 seconds. No pressure. This estimate is not a diagnosis.</p>

        <!-- Your Provider card -->
        <div class="bg-card shadow-[var(--shadow-soft)] mt-12 p-6 sm:p-8 border border-border rounded-3xl text-left">
          <div class="flex items-start gap-4">
            <div class="flex flex-none justify-center items-center bg-accent/60 rounded-2xl w-14 h-14 text-primary">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><path d="M11 2v2"/><path d="M5 2v2"/><path d="M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1"/><path d="M8 15a6 6 0 0 0 12 0v-3"/><circle cx="20" cy="10" r="2"/></svg>
            </div>
            <div class="flex-1">
              <p class="font-medium text-[11px] text-primary uppercase tracking-wide">Your provider</p>
              <h3 class="mt-1 font-semibold text-foreground text-lg"><?= esc_html( $s['intro_provider_name'] ?? ( 'Dr. ' . $brand . ' Dental Sleep Team' ) ) ?></h3>
              <p class="mt-2 text-muted-foreground text-sm leading-relaxed"><?= esc_html( $s['intro_provider_bio'] ?? ( $brand . "'s sleep team focuses on airway-centred dentistry — custom oral appliances, snoring support, and myofunctional care for adults across " . $city . ". We work alongside your physician or sleep specialist when needed, so your treatment plan stays coordinated." ) ) ?></p>
              <div class="flex flex-wrap gap-2 mt-4">
                <span class="inline-flex items-center gap-1.5 bg-secondary px-3 py-1 rounded-full font-medium text-[11px] text-foreground">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 text-primary"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>
                  Airway-focused care
                </span>
                <span class="inline-flex items-center gap-1.5 bg-secondary px-3 py-1 rounded-full font-medium text-[11px] text-foreground">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 text-primary"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>
                  <?= esc_html( $city ) ?>
                </span>
                <span class="inline-flex items-center gap-1.5 bg-secondary px-3 py-1 rounded-full font-medium text-[11px] text-foreground">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3 text-primary"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/><path d="M3.22 13H9.5l.5-1 2 4.5 2-7 1.5 3.5h5.27"/></svg>
                  Custom sleep appliances
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </section>

  <!-- FORM -->
  <section data-view="form" class="hidden">
    <header class="top-0 z-10 sticky bg-background/80 backdrop-blur border-border/60 border-b">
      <div class="flex justify-between items-center mx-auto px-5 py-4 max-w-2xl">
        <a href="<?= esc_url( $brand_url ) ?>" class="flex items-center gap-2 font-medium text-foreground text-sm no-underline">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-primary"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path></svg>
          <?= esc_html( $brand ) ?>
        </a>
        <span id="sapn-step-label" class="text-muted-foreground text-xs">Step 1 of 7</span>
      </div>
      <div class="bg-secondary w-full h-1"><div id="sapn-progress" class="bg-primary h-full transition-all duration-500 ease-out" style="width:14.28%"></div></div>
    </header>
    <main class="mx-auto px-5 py-8 sm:py-12 max-w-2xl">
      <div id="sapn-step" class=""></div>
      <div class="flex justify-between items-center mt-8">
        <button type="button" id="sapn-back" data-action="back" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full text-muted-foreground hover:text-foreground text-sm transition">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="m12 19-7-7 7-7"></path><path d="M19 12H5"></path></svg>
          Back
        </button>
        <p class="text-muted-foreground text-xs">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="inline mr-1 w-3.5 h-3.5"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path><path d="m9 12 2 2 4-4"></path></svg>
          Not a diagnosis
        </p>
      </div>
    </main>
  </section>

  <!-- RESULTS -->
  <section data-view="results" class="hidden">
    <header class="top-0 z-10 sticky bg-background/80 backdrop-blur border-border/60 border-b">
      <div class="flex justify-between items-center mx-auto px-5 py-4 max-w-2xl">
        <a href="<?= esc_url( $brand_url ) ?>" class="flex items-center gap-2 font-medium text-foreground text-sm no-underline">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5 text-primary"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"></path><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"></path></svg>
          <?= esc_html( $brand ) ?>
        </a>
        <span class="text-muted-foreground text-xs">Step 7 of 7</span>
      </div>
      <div class="bg-secondary w-full h-1"><div class="bg-primary h-full" style="width:100%"></div></div>
    </header>
    <main class="mx-auto px-5 py-8 sm:py-12 max-w-2xl">
      <div id="sapn-results" class=""></div>
    </main>
  </section>

  <!-- Honeypot -->
  <input type="text" name="sapn_hp_website" value="" tabindex="-1" autocomplete="off"
         style="position:absolute;left:-9999px;opacity:0;height:0;width:0;" aria-hidden="true"/>
</div>

<script>
(function(){
  'use strict';
  var ROOT      = document.currentScript.previousElementSibling;
  while (ROOT && !ROOT.classList.contains('sapn-app')) ROOT = ROOT.previousElementSibling;
  if (!ROOT) return;
  var AJAX      = <?= wp_json_encode( $ajax_url ) ?>;
  var NONCE     = <?= wp_json_encode( $nonce ) ?>;
  var BOOK_LBL  = <?= wp_json_encode( $s['result_book_label'] ) ?>;
  var BOOK_URL  = <?= wp_json_encode( $booking ) ?>;
  var INS_LBL   = <?= wp_json_encode( $s['result_insurance_label'] ) ?>;
  var INS_URL   = <?= wp_json_encode( $ins_url ) ?>;
  var NEXT_STEPS = <?= wp_json_encode( [
    'show'    => ( $s['next_steps_show'] ?? '0' ) === '1',
    'heading' => $s['next_steps_heading'] ?? '',
    'body'    => $s['next_steps_body']    ?? '',
    'l1_lbl'  => $s['next_steps_link1_label'] ?? '',
    'l1_url'  => $s['next_steps_link1_url']   ?? '',
    'l2_lbl'  => $s['next_steps_link2_label'] ?? '',
    'l2_url'  => $s['next_steps_link2_url']   ?? '',
    'l3_lbl'  => $s['next_steps_link3_label'] ?? '',
    'l3_url'  => $s['next_steps_link3_url']   ?? '',
    'hint'    => $s['next_steps_hint'] ?? '',
  ] ) ?>;

  // ── ICONS ──
  var ICONS = {
    volume:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4.5 h-4.5"><path d="M11 4.702a.705.705 0 0 0-1.203-.498L6.413 7.587A1.4 1.4 0 0 1 5.416 8H3a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h2.416a1.4 1.4 0 0 1 .997.413l3.383 3.384A.705.705 0 0 0 11 19.298z"/><path d="M16 9a5 5 0 0 1 0 6"/><path d="M19.364 18.364a9 9 0 0 0 0-12.728"/></svg>',
    stethoscope:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4.5 h-4.5"><path d="M11 2v2"/><path d="M5 2v2"/><path d="M5 3H4a2 2 0 0 0-2 2v4a6 6 0 0 0 12 0V5a2 2 0 0 0-2-2h-1"/><path d="M8 15a6 6 0 0 0 12 0v-3"/><circle cx="20" cy="10" r="2"/></svg>',
    wind:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4.5 h-4.5"><path d="M12.8 19.6A2 2 0 1 0 14 16H2"/><path d="M17.5 8a2.5 2.5 0 1 1 2 4H2"/><path d="M9.8 4.4A2 2 0 1 1 11 8H2"/></svg>',
    moon:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4.5 h-4.5"><path d="M20.985 12.486a9 9 0 1 1-9.473-9.472c.405-.022.617.46.402.803a6 6 0 0 0 8.268 8.268c.344-.215.825-.004.803.401"/></svg>',
    smile:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4.5 h-4.5"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" x2="9.01" y1="9" y2="9"/><line x1="15" x2="15.01" y1="9" y2="9"/></svg>',
    question:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4.5 h-4.5"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><path d="M12 17h.01"/></svg>',
    leaf:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.48 19 2c1 2 2 4.18 2 8 0 5.5-4.78 10-10 10Z"/><path d="M2 21c0-3 1.85-5.36 5.08-6C9.5 14.52 12 13 13 12"/></svg>',
    sparkles:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-7 h-7"><path d="M11.017 2.814a1 1 0 0 1 1.966 0l1.051 5.558a2 2 0 0 0 1.594 1.594l5.558 1.051a1 1 0 0 1 0 1.966l-5.558 1.051a2 2 0 0 0-1.594 1.594l-1.051 5.558a1 1 0 0 1-1.966 0l-1.051-5.558a2 2 0 0 0-1.594-1.594l-5.558-1.051a1 1 0 0 1 0-1.966l5.558-1.051a2 2 0 0 0 1.594-1.594z"/><path d="M20 2v4"/><path d="M22 4h-4"/><circle cx="4" cy="20" r="2"/></svg>',
    shield:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/><path d="m9 12 2 2 4-4"/></svg>',
    check:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-none mt-0.5 w-4 h-4 text-primary"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
    arrowRight:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>',
    checkSmall:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" class="w-3 h-3"><polyline points="20 6 9 17 4 12"/></svg>'
  };

  var QUESTIONS = [
    { id:'reason', type:'single', autoAdvance:true,
      title:'What brought you here today?',
      subtitle:'Choose the option that feels closest. You can still qualify for more than one treatment path.',
      options:[
        { value:'snoring', label:'I snore or my partner says I snore', icon:'volume' },
        { value:'apnea',   label:'I was diagnosed with sleep apnea',    icon:'stethoscope' },
        { value:'cpap',    label:'I use CPAP but struggle with it',     icon:'wind' },
        { value:'tired',   label:'I wake up tired or unrefreshed',      icon:'moon' },
        { value:'airway',  label:'I mouth-breathe or have tongue posture concerns', icon:'smile' },
        { value:'explore', label:"I'm not sure — I just want to explore my options", icon:'question' }
      ] },
    { id:'study', type:'single', autoAdvance:true,
      title:'Have you had a sleep study or been told you have sleep apnea?',
      subtitle:'This helps us understand whether you may be ready for a custom appliance discussion, or whether the first step is an evaluation.',
      why:'Mild and moderate cases are often well suited for an oral appliance; severe cases usually still need a coordinated plan with your sleep physician.',
      options:[
        { value:'mild',     label:'Yes — mild sleep apnea' },
        { value:'moderate', label:'Yes — moderate sleep apnea' },
        { value:'severe',   label:'Yes — severe sleep apnea' },
        { value:'unknown',  label:"I had a sleep study, but I'm not sure of the result" },
        { value:'suspect',  label:'No, but I suspect I may have sleep apnea' },
        { value:'snoring',  label:"No — I'm mainly concerned about snoring" }
      ] },
    { id:'cpap', type:'single', autoAdvance:true,
      title:'What has your experience with CPAP been like?',
      subtitle:'Many patients explore oral appliance therapy because they want something quieter, smaller, or easier to use consistently.',
      why:"If CPAP is working well, an oral appliance may not be needed. If it isn't, we'll review alternatives that may fit you better.",
      options:[
        { value:'works',       label:'I use CPAP and it works well' },
        { value:'intolerant',  label:"I tried CPAP but couldn't tolerate it" },
        { value:'sometimes',   label:'I use CPAP sometimes, but not consistently' },
        { value:'recommended', label:"CPAP was recommended, but I haven't started" },
        { value:'never',       label:"I've never been prescribed CPAP" },
        { value:'unsure',      label:'Not sure' }
      ] },
    { id:'symptoms', type:'multi',
      title:'Which of these sound familiar?', subtitle:'Select all that apply.',
      options:[
        { value:'loud_snoring', label:'Loud or frequent snoring' },
        { value:'waking_tired', label:'Waking up tired' },
        { value:'headaches',    label:'Morning headaches or dry mouth' },
        { value:'pauses',       label:'Partner notices pauses in breathing' },
        { value:'none',         label:'None of these / not sure', exclusive:true }
      ] },
    { id:'airway', type:'multi',
      title:'Do you notice any mouth breathing or tongue posture concerns?',
      subtitle:'These patterns can sometimes be part of an airway-focused treatment plan.',
      options:[
        { value:'mouth_breathe', label:'I mouth-breathe or wake with a dry mouth' },
        { value:'tongue',        label:'Tongue posture or swallowing concerns' },
        { value:'none',          label:'No / not sure', exclusive:true }
      ] },
    { id:'contact', type:'contact',
      title:"Almost there — let's unlock your estimate",
      subtitle:'Your personalized range is ready. Enter your details to reveal it and your suggested next step.' }
  ];

  var TOTAL_STEPS = QUESTIONS.length + 1; // +1 for results
  var state = { currentStep: 0, answers: {}, transitioning: false };

  function q(sel){ return ROOT.querySelector(sel); }
  function qa(sel){ return ROOT.querySelectorAll(sel); }
  var stepLabel    = q('#sapn-step-label');
  var progressBar  = q('#sapn-progress');
  var stepEl       = q('#sapn-step');
  var resultsEl    = q('#sapn-results');
  var backBtn      = q('#sapn-back');

  function showView(name, after) {
    var next    = ROOT.querySelector('[data-view="' + name + '"]');
    var current = ROOT.querySelector('[data-view]:not(.hidden)');
    if (!next || current === next) { if (after) after(); return; }
    var finish = function(){
      if (current) { current.classList.add('hidden'); current.classList.remove('view-leaving'); }
      next.classList.remove('hidden');
      next.classList.add('view-entering');
      void next.offsetWidth;
      requestAnimationFrame(function(){ next.classList.remove('view-entering'); });
      try { window.scrollTo({ top:0, behavior:'instant' in window ? 'instant' : 'auto' }); } catch(e){ window.scrollTo(0,0); }
      if (after) after();
    };
    if (current) { current.classList.add('view-leaving'); setTimeout(finish, 280); }
    else finish();
  }

  function renderLoaderHtml(text) {
    return '<div class="rw-loader-wrap"><div class="rw-loader-mark">' + ICONS.leaf + '</div><p class="rw-loader-text">' + text + '…</p><div class="rw-loader-track" role="progressbar" aria-label="Loading"></div></div>';
  }

  function animOut(el, cb) {
    el.classList.remove('step-entering');
    el.classList.add('step-leaving');
    setTimeout(function(){
      el.classList.remove('step-leaving');
      if (cb) cb();
    }, 220);
  }
  function animIn(el) {
    el.classList.remove('step-entering');
    void el.offsetWidth;
    el.classList.add('step-entering');
    setTimeout(function(){ el.classList.remove('step-entering'); }, 320);
  }

  function loadInForm(destIdx, text, after, delay) {
    if (state.transitioning) return;
    state.transitioning = true;
    // 1. Fade the current step out
    animOut(stepEl, function(){
      // 2. Swap to loader and fade it in
      stepEl.innerHTML = renderLoaderHtml(text);
      animIn(stepEl);
      // 3. Hold the loader for `delay`, then fade it out
      setTimeout(function(){
        animOut(stepEl, function(){
          // 4. Render the destination step and fade it in
          if (destIdx !== null && destIdx !== undefined) state.currentStep = destIdx;
          try { if (after) after(); } finally {
            animIn(stepEl);
            state.transitioning = false;
          }
        });
      }, delay || 800);
    });
  }

  function fmtMoney(n){ return '$' + n.toLocaleString('en-US'); }
  function calculatePrice() {
    var a = state.answers, min = 0, max = 250;
    if (a.reason) max = 4000;
    if (a.study) {
      if (['mild','moderate','severe','suspect','unknown'].indexOf(a.study) >= 0) { min = 1500; max = 4000; }
      if (a.study === 'severe') max = 5500;
    }
    if (a.study === 'snoring' && a.reason !== 'apnea') { min = 1500; max = 3500; }
    if (a.cpap === 'works' && ['mild','moderate','severe'].indexOf(a.study) < 0) min = 0;
    if (Array.isArray(a.symptoms)) {
      var sx = a.symptoms.filter(function(v){ return v !== 'none'; }).length;
      if (sx >= 3) max = Math.max(max, 5500);
      if (sx >= 1 && min === 0) min = 1500;
    }
    if (Array.isArray(a.airway) && a.airway.some(function(v){ return v !== 'none'; })) {
      max = Math.max(max, min + 4000);
    }
    return { min: min, max: max };
  }
  // Single-value display: collapse the [min, max] range to one number (use the max as the
  // headline estimate so the user sees the full investment, not a range).
  function singlePrice() { return calculatePrice().max; }
  function priceString() { return fmtMoney(singlePrice()); }

  function updateHeader() {
    var n = state.currentStep + 1;
    stepLabel.textContent = 'Step ' + n + ' of ' + TOTAL_STEPS;
    progressBar.style.width = ((n / TOTAL_STEPS) * 100) + '%';
    backBtn.style.visibility = state.currentStep === 0 ? 'hidden' : 'visible';
  }

  function whyButtonHtml(text){ return text ? '<button type="button" data-action="toggle-why" aria-label="Why we ask" class="inline-flex items-center gap-1 bg-background mt-1 px-2.5 border border-border hover:border-primary/40 rounded-full h-7 font-medium text-[11px] text-muted-foreground hover:text-foreground transition">' + ICONS.question.replace('w-4.5 h-4.5','w-3.5 h-3.5') + 'Why</button>' : ''; }
  function whyPanelHtml(text){ return text ? '<div data-why class="hidden mt-3 bg-secondary/40 px-4 py-3 border border-border/60 rounded-xl text-muted-foreground text-xs leading-relaxed">' + text + '</div>' : ''; }

  function renderSingle(qd){
    var selected = state.answers[qd.id];
    var opts = qd.options.map(function(o){
      var sel = selected === o.value;
      var iconHtml = o.icon ? '<span class="opt-icon-wrap flex flex-none justify-center items-center bg-secondary rounded-xl w-9 h-9 text-muted-foreground">' + (ICONS[o.icon] || '') + '</span>' : '';
      return '<button type="button" data-action="select-single" data-value="' + o.value + '" class="opt group flex items-center gap-3 bg-card hover:bg-accent/20 p-4 border border-border hover:border-primary/40 rounded-2xl w-full text-left transition-all' + (sel ? ' opt-selected' : '') + '">' +
        iconHtml +
        '<span class="flex-1 text-foreground text-sm sm:text-base">' + o.label + '</span>' +
        '<span class="opt-radio flex flex-none justify-center items-center border border-border rounded-full w-5 h-5"><span class="opt-check">' + ICONS.checkSmall + '</span></span>' +
        '</button>';
    }).join('');
    return '<div><div class="mb-6"><div class="flex items-start gap-2"><h2 class="flex-1 font-semibold text-foreground text-2xl sm:text-3xl tracking-tight">' + qd.title + '</h2>' + whyButtonHtml(qd.why) + '</div><p class="mt-2 text-muted-foreground text-sm leading-relaxed">' + qd.subtitle + '</p>' + whyPanelHtml(qd.why) + '</div><div class="gap-2.5 grid">' + opts + '</div></div>';
  }

  function renderMulti(qd){
    var selected = Array.isArray(state.answers[qd.id]) ? state.answers[qd.id] : [];
    var opts = qd.options.map(function(o){
      var sel = selected.indexOf(o.value) >= 0;
      return '<button type="button" data-action="select-multi" data-value="' + o.value + '"' + (o.exclusive ? ' data-exclusive="1"' : '') + ' class="opt group flex items-center gap-3 bg-card hover:bg-accent/20 p-4 border border-border hover:border-primary/40 rounded-2xl w-full text-left transition-all' + (sel ? ' opt-selected' : '') + '">' +
        '<span class="flex-1 text-foreground text-sm sm:text-base">' + o.label + '</span>' +
        '<span class="opt-radio flex flex-none justify-center items-center border border-border rounded-md w-5 h-5"><span class="opt-check">' + ICONS.checkSmall + '</span></span>' +
        '</button>';
    }).join('');
    var canContinue = selected.length > 0;
    return '<div><div class="mb-6"><div class="flex items-start gap-2"><h2 class="flex-1 font-semibold text-foreground text-2xl sm:text-3xl tracking-tight">' + qd.title + '</h2></div><p class="mt-2 text-muted-foreground text-sm leading-relaxed">' + qd.subtitle + '</p></div><div class="gap-2.5 grid">' + opts + '</div><button type="button" data-action="continue"' + (canContinue ? '' : ' disabled') + ' class="inline-flex justify-center items-center gap-2 bg-primary hover:opacity-95 disabled:opacity-40 mt-6 px-6 py-3.5 rounded-full w-full font-medium text-primary-foreground text-sm transition">Continue ' + ICONS.arrowRight + '</button></div>';
  }

  function renderContact(qd){
    var c = state.answers.contact || {};
    function pillsHtml(group, options, stack) {
      return options.map(function(o){
        var active = c[group] === o.value;
        var cls = stack
          ? 'flex flex-col items-center gap-1 bg-background px-2 py-3 border border-border rounded-xl text-muted-foreground text-xs transition'
          : 'bg-background px-2 py-2.5 border border-border rounded-xl text-muted-foreground text-xs transition';
        return '<button type="button" data-pill="' + group + '" data-value="' + o.value + '" class="' + cls + (active ? ' pill-active' : '') + '">' + (o.icon || '') + o.label + '</button>';
      }).join('');
    }
    var contactPrefOpts = [
      { value:'text',  label:'Text',       icon:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M22 17a2 2 0 0 1-2 2H6.828a2 2 0 0 0-1.414.586l-2.202 2.202A.71.71 0 0 1 2 21.286V5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2z"/></svg>' },
      { value:'phone', label:'Phone call', icon:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384"/></svg>' },
      { value:'email', label:'Email',      icon:'<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-4 h-4"><path d="m22 7-8.991 5.727a2 2 0 0 1-2.009 0L2 7"/><rect x="2" y="4" width="20" height="16" rx="2"/></svg>' }
    ];
    var benefitsOpts = [ { value:'yes', label:'Yes' }, { value:'unsure', label:'Not sure' }, { value:'no', label:'No' } ];

    var trustBadges =
      '<div class="gap-2 grid grid-cols-3 mt-4 text-center">' +
        '<div class="bg-card/60 p-2.5 border border-border rounded-xl"><span class="mx-auto mb-1 block w-4 h-4 text-primary">' + ICONS.shield + '</span><p class="text-[10px] text-muted-foreground leading-tight">Private &amp; secure</p></div>' +
        '<div class="bg-card/60 p-2.5 border border-border rounded-xl"><span class="mx-auto mb-1 block w-4 h-4 text-primary">' + ICONS.leaf.replace('w-7 h-7','w-4 h-4') + '</span><p class="text-[10px] text-muted-foreground leading-tight">No spam, ever</p></div>' +
        '<div class="bg-card/60 p-2.5 border border-border rounded-xl"><span class="mx-auto mb-1 block w-4 h-4 text-primary">' + ICONS.sparkles.replace('w-7 h-7','w-4 h-4') + '</span><p class="text-[10px] text-muted-foreground leading-tight">Instant estimate</p></div>' +
      '</div>';

    return '<form data-form="contact">' +
      '<div class="mb-6"><h2 class="font-semibold text-foreground text-2xl sm:text-3xl tracking-tight">' + qd.title + '</h2><p class="mt-2 text-muted-foreground text-sm leading-relaxed">' + qd.subtitle + '</p></div>' +
      '<div class="relative bg-accent/20 mb-6 p-5 border border-primary/20 rounded-2xl overflow-hidden">' +
        '<div class="blur-[6px] select-none">' +
          '<p class="font-medium text-[11px] text-primary uppercase tracking-wide">Your estimated investment</p>' +
          '<p class="mt-1 font-semibold tabular-nums text-foreground text-3xl tracking-tight">' + priceString() + '</p>' +
          '<p class="mt-2 text-muted-foreground text-xs">Sleep consultation · Custom oral appliance · Follow-up care</p>' +
        '</div>' +
        '<div class="absolute inset-0 flex justify-center items-center bg-gradient-to-b from-background/40 to-background/80 pointer-events-none">' +
          '<span class="inline-flex items-center gap-1.5 bg-primary/95 shadow-[var(--shadow-soft)] px-3 py-1.5 rounded-full font-medium text-primary-foreground text-xs">' + ICONS.shield.replace('w-4 h-4','w-3.5 h-3.5') + 'Unlock below</span>' +
        '</div>' +
      '</div>' +
      '<div class="gap-4 grid bg-card shadow-[var(--shadow-card)] p-5 border border-border rounded-2xl">' +
        '<label class="block"><span class="block mb-1.5 font-medium text-muted-foreground text-xs">First Name</span><input name="firstName" maxlength="80" value="' + (c.firstName || '').replace(/"/g,'&quot;') + '" class="bg-background px-4 py-3 border border-input focus:border-primary rounded-xl outline-none focus:ring-2 focus:ring-primary/20 w-full text-sm"/></label>' +
        '<label class="block"><span class="block mb-1.5 font-medium text-muted-foreground text-xs">Email Address</span><input name="email" type="email" maxlength="200" value="' + (c.email || '').replace(/"/g,'&quot;') + '" class="bg-background px-4 py-3 border border-input focus:border-primary rounded-xl outline-none focus:ring-2 focus:ring-primary/20 w-full text-sm"/></label>' +
        '<label class="block"><span class="block mb-1.5 font-medium text-muted-foreground text-xs">Phone Number (optional)</span><input name="phone" type="tel" maxlength="30" value="' + (c.phone || '').replace(/"/g,'&quot;') + '" class="bg-background px-4 py-3 border border-input focus:border-primary rounded-xl outline-none focus:ring-2 focus:ring-primary/20 w-full text-sm"/></label>' +
        '<div class="block"><span class="block mb-1.5 font-medium text-muted-foreground text-xs">Preferred way to be contacted (optional)</span><div class="gap-2 grid grid-cols-3">' + pillsHtml('contactPref', contactPrefOpts, true) + '</div></div>' +
        '<div class="block"><span class="block mb-1.5 font-medium text-muted-foreground text-xs">Do you have extended health benefits? (optional)</span><div class="gap-2 grid grid-cols-3">' + pillsHtml('benefits', benefitsOpts, false) + '</div></div>' +
      '</div>' +
      trustBadges +
      '<button type="submit" data-action="submit-contact" disabled class="inline-flex justify-center items-center gap-2 bg-primary hover:opacity-95 disabled:opacity-40 mt-6 px-6 py-4 rounded-full w-full font-medium text-primary-foreground text-base transition">Reveal My Estimate ' + ICONS.arrowRight + '</button>' +
      '<p data-error class="hidden mt-3 text-red-600 text-xs text-center"></p>' +
      '<p class="mt-3 text-muted-foreground text-xs text-center">We respect your privacy. No spam — just your estimate and next-step information.</p>' +
    '</form>';
  }

  function renderStep() {
    var qd = QUESTIONS[state.currentStep];
    var html = '';
    if (qd.type === 'single')  html = renderSingle(qd);
    else if (qd.type === 'multi') html = renderMulti(qd);
    else if (qd.type === 'contact') html = renderContact(qd);
    stepEl.innerHTML = html;
    updateHeader();
    if (qd.type === 'contact') wireContactForm();
  }

  function renderResults() {
    var c = state.answers.contact || {};
    var includesAirway = Array.isArray(state.answers.airway) && state.answers.airway.some(function(v){ return v !== 'none'; });
    // Single-value line items (no ranges).
    var lines = [
      { name:'Sleep consultation',    value: 250 },
      { name:'Custom oral appliance', sub:'Includes fitting &amp; calibration', value: 4000 },
      { name:'Follow-up care',        value: 500 }
    ];
    if (includesAirway) {
      lines.push({ name:'Myofunctional assessment',     value: 300 });
      lines.push({ name:'Myofunctional therapy program', sub:'If recommended', value: 1500 });
    }
    var subTotal = lines.reduce(function(s,l){ return s + l.value; }, 0);
    var linesHtml = lines.map(function(l){
      return '<li class="flex justify-between items-start gap-4 px-5 py-3.5"><div class="flex-1 min-w-0"><p class="font-medium text-foreground text-sm leading-snug">' + l.name + '</p>' + (l.sub ? '<p class="mt-0.5 text-muted-foreground text-xs leading-snug">' + l.sub + '</p>' : '') + '</div><p class="flex-none font-medium tabular-nums text-foreground text-sm leading-snug">' + fmtMoney(l.value) + '</p></li>';
    }).join('');

    var bookBtn = BOOK_URL
      ? '<a href="' + BOOK_URL + '" class="inline-flex justify-center items-center gap-2 bg-primary hover:opacity-95 px-6 py-3.5 rounded-full w-full font-medium text-primary-foreground text-sm transition">' + BOOK_LBL + ' ' + ICONS.arrowRight + '</a>'
      : '<button type="button" class="inline-flex justify-center items-center gap-2 bg-primary hover:opacity-95 px-6 py-3.5 rounded-full w-full font-medium text-primary-foreground text-sm transition">' + BOOK_LBL + ' ' + ICONS.arrowRight + '</button>';
    var insBtn = INS_URL
      ? '<a href="' + INS_URL + '" class="inline-flex justify-center items-center bg-background hover:bg-accent/30 px-6 py-3.5 border border-border rounded-full w-full font-medium text-foreground text-sm transition">' + INS_LBL + '</a>'
      : '<button type="button" class="inline-flex justify-center items-center bg-background hover:bg-accent/30 px-6 py-3.5 border border-border rounded-full w-full font-medium text-foreground text-sm transition">' + INS_LBL + '</button>';

    var bullets = [
      'Designed to be worn during sleep',
      'Custom-fitted to your teeth and jaw',
      'Quiet, compact, and travel-friendly',
      'May be an option for snoring or appropriate sleep apnea cases'
    ];
    var bulletsHtml = '<ul class="space-y-2 mt-5">' + bullets.map(function(t){
      return '<li class="flex items-start gap-2 text-foreground text-sm">' + ICONS.check + '<span>' + t + '</span></li>';
    }).join('') + '</ul>';

    var myofunctionalCard = includesAirway
      ? '<div class="bg-card mt-5 p-5 border border-border rounded-2xl">' +
          '<p class="font-medium text-muted-foreground text-xs uppercase tracking-wide">May also support your plan</p>' +
          '<h4 class="mt-2 font-semibold text-foreground text-base">Myofunctional therapy may be part of your treatment path.</h4>' +
          '<p class="mt-1.5 text-muted-foreground text-sm leading-relaxed">Myofunctional therapy focuses on training the muscles of the mouth, tongue, and face to support healthier breathing patterns, oral rest posture, and airway-related function.</p>' +
        '</div>'
      : '';

    function escAttr(s) { return String(s).replace(/&/g,'&amp;').replace(/"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function escText(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    function nsLink(label, url, extraClass) {
      if (!label || !url) return '';
      return '<a href="' + escAttr(url) + '" target="_blank" rel="noopener" class="font-semibold text-primary underline underline-offset-2 hover:opacity-80' + (extraClass ? ' ' + extraClass : '') + '">' + escText(label) + '</a>';
    }
    var nextStepsCard = '';
    if (NEXT_STEPS.show && (NEXT_STEPS.heading || NEXT_STEPS.body)) {
      var q1 = nsLink(NEXT_STEPS.l1_lbl, NEXT_STEPS.l1_url);
      var q2 = nsLink(NEXT_STEPS.l2_lbl, NEXT_STEPS.l2_url);
      var q3 = nsLink(NEXT_STEPS.l3_lbl + ' →', NEXT_STEPS.l3_url);
      var quizLine = '';
      if (q1 && q2)      quizLine = ' ' + q1 + ' and ' + q2 + '.';
      else if (q1 || q2) quizLine = ' ' + (q1 || q2) + '.';
      var hintLine = '';
      if (q3) hintLine = '<p class="mt-3 text-muted-foreground text-sm leading-relaxed">' + escText(NEXT_STEPS.hint || '') + ' ' + q3 + '</p>';
      nextStepsCard =
        '<div class="bg-card shadow-[var(--shadow-card)] mt-5 p-5 sm:p-6 border border-border rounded-2xl">' +
          (NEXT_STEPS.heading ? '<h4 class="font-semibold text-foreground text-base sm:text-lg tracking-tight">' + escText(NEXT_STEPS.heading) + '</h4>' : '') +
          (NEXT_STEPS.body ? '<p class="mt-2 text-muted-foreground text-sm leading-relaxed">' + escText(NEXT_STEPS.body) + quizLine + '</p>' : '') +
          hintLine +
        '</div>';
    }

    resultsEl.innerHTML =
      '<div class="text-center"><div class="flex justify-center items-center bg-accent/60 mx-auto mb-4 rounded-2xl w-14 h-14 text-primary">' + ICONS.sparkles + '</div><p class="text-muted-foreground text-sm">Thanks' + (c.firstName ? ', ' + c.firstName : '') + ' — here\'s your personalized estimate</p></div>' +
      '<div class="bg-card shadow-[var(--shadow-soft)] mt-6 border border-primary/20 rounded-3xl overflow-hidden">' +
        '<div class="bg-accent/30 p-6 sm:p-8 border-border/60 border-b">' +
          '<p class="font-medium text-primary text-xs uppercase tracking-wide">Your estimated investment</p>' +
          '<p class="mt-2 font-semibold tabular-nums text-foreground text-3xl sm:text-4xl tracking-tight">' + priceString() + '</p>' +
          '<p class="mt-2 text-muted-foreground text-xs">Before insurance · CAD</p>' +
        '</div>' +
        '<div class="p-6 sm:p-8">' +
          '<h3 class="font-semibold text-foreground text-lg sm:text-xl tracking-tight">Your answers suggest a custom oral appliance may be worth exploring.</h3>' +
          '<p class="mt-2 text-muted-foreground text-sm leading-relaxed">This may include a custom-fitted sleep appliance, fitting, calibration, and follow-up care depending on your final treatment plan.</p>' +
          '<div class="bg-background/60 mt-5 border border-border rounded-2xl overflow-hidden">' +
            '<p class="px-5 py-3 bg-secondary/40 border-border/60 border-b font-semibold text-muted-foreground text-[11px] uppercase tracking-[.08em] text-center">What your estimate may include</p>' +
            '<ul class="divide-y divide-border/60">' + linesHtml +
              '<li class="flex justify-between items-center bg-secondary/60 px-5 py-3.5 border-border/60 border-t"><p class="font-semibold text-foreground text-[11px] uppercase tracking-[.08em]">Subtotal</p><p class="font-semibold tabular-nums text-foreground text-sm">' + fmtMoney(subTotal) + '</p></li>' +
            '</ul>' +
          '</div>' +
          bulletsHtml +
          '<div class="flex flex-col gap-2.5 mt-6">' + bookBtn + insBtn + '</div>' +
        '</div>' +
      '</div>' +
      myofunctionalCard +
      nextStepsCard +
      '<div class="bg-secondary/40 mt-8 p-5 border border-border/60 rounded-2xl text-muted-foreground text-xs leading-relaxed">' +
        '<span class="block mb-1.5 w-4 h-4 text-primary">' + ICONS.shield + '</span>' +
        'This estimate is for educational purposes only and is not a diagnosis or treatment plan. Final treatment recommendations, fees, and insurance eligibility are confirmed after a clinical exam and review of your sleep history.' +
      '</div>';
  }

  // ── Contact form wiring ──
  function wireContactForm() {
    var form = ROOT.querySelector('[data-form="contact"]');
    if (!form) return;
    function syncSubmit() {
      var first = form.querySelector('input[name="firstName"]').value.trim();
      var email = form.querySelector('input[name="email"]').value.trim();
      var valid = first.length > 0 && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
      form.querySelector('[data-action="submit-contact"]').disabled = !valid;
    }
    form.addEventListener('input', function(e){
      if (e.target.matches('input')) {
        var c = state.answers.contact || (state.answers.contact = {});
        c[e.target.name] = e.target.value;
        syncSubmit();
      }
    });
    form.addEventListener('click', function(e){
      var pill = e.target.closest('[data-pill]');
      if (pill) {
        e.preventDefault();
        var group = pill.getAttribute('data-pill'), val = pill.getAttribute('data-value');
        var c = state.answers.contact || (state.answers.contact = {});
        c[group] = c[group] === val ? null : val;
        form.querySelectorAll('[data-pill="' + group + '"]').forEach(function(b){
          b.classList.toggle('pill-active', c[group] === b.getAttribute('data-value'));
        });
      }
    });
    form.addEventListener('submit', function(e){
      e.preventDefault();
      submitToServer(form);
    });
    syncSubmit();
  }

  // ── UTM capture (case-insensitive, sessionStorage fallback) ──
  function _getParam(k) {
    var up = new URLSearchParams(window.location.search);
    var ss = {};
    try { ss = JSON.parse(sessionStorage.getItem('scad_tracking_params') || '{}'); } catch(e){}
    var v = '';
    up.forEach(function(val, key){ if (key.toLowerCase() === k) v = val; });
    return v || ss[k] || '';
  }

  function submitToServer(form) {
    var c = state.answers.contact || {};
    var hp = ROOT.querySelector('[name="sapn_hp_website"]');
    var payload = {
      action:        'sapn_submit',
      sapn_nonce:    NONCE,
      sapn_hp:       hp ? hp.value : '',
      firstName:     c.firstName || '',
      email:         c.email || '',
      phone:         c.phone || '',
      reason:        state.answers.reason || '',
      study:         state.answers.study  || '',
      cpap:          state.answers.cpap   || '',
      symptoms:      (state.answers.symptoms || []).join(', '),
      airway:        (state.answers.airway   || []).join(', '),
      estimateRange: fmtMoney(singlePrice()),
      contactPref:   c.contactPref || '',
      benefits:      c.benefits    || '',
      utmcampaign_custom: _getParam('utmcampaign_custom'),
      utmmedium_custom:   _getParam('utmmedium_custom'),
      utmcontent_custom:  _getParam('utmcontent_custom'),
      utmkeyword_custom:  _getParam('utmkeyword_custom'),
      utmterm_custom:     _getParam('utmterm_custom'),
      gclid_custom:       _getParam('gclid_custom')
    };

    state.transitioning = true;
    stepEl.innerHTML = renderLoaderHtml('Unlocking your personalized estimate');

    fetch(AJAX, {
      method: 'POST',
      headers: { 'Content-Type':'application/x-www-form-urlencoded' },
      body: Object.keys(payload).map(function(k){ return encodeURIComponent(k) + '=' + encodeURIComponent(payload[k]); }).join('&')
    }).then(function(r){ return r.json(); }).then(function(d){
      if (d && d.success) {
        renderResults();
        showView('results', function(){ state.transitioning = false; });
      } else {
        state.transitioning = false;
        renderStep(); // restore the contact form
        setTimeout(function(){
          var e = ROOT.querySelector('[data-form="contact"] [data-error]');
          if (e) { e.textContent = (d && d.data) || 'Submission failed. Please try again.'; e.classList.remove('hidden'); }
        }, 50);
      }
    }).catch(function(){
      state.transitioning = false;
      renderStep();
      setTimeout(function(){
        var e = ROOT.querySelector('[data-form="contact"] [data-error]');
        if (e) { e.textContent = 'Something went wrong. Please try again.'; e.classList.remove('hidden'); }
      }, 50);
    });
  }

  // ── Interactions ──
  function selectSingle(value) {
    var qd = QUESTIONS[state.currentStep];
    state.answers[qd.id] = value;
    if (qd.autoAdvance) {
      // Render the selected state so the user sees their option animate in
      // (radio fills with a pop + check), then transition to the loader.
      renderStep();
      setTimeout(advance, 380);
    } else {
      renderStep();
    }
  }
  function selectMulti(value, exclusive) {
    var qd = QUESTIONS[state.currentStep];
    var current = Array.isArray(state.answers[qd.id]) ? state.answers[qd.id].slice() : [];
    var excl = qd.options.filter(function(o){ return o.exclusive; }).map(function(o){ return o.value; });
    if (exclusive) current = current.indexOf(value) >= 0 ? [] : [value];
    else {
      current = current.filter(function(v){ return excl.indexOf(v) < 0; });
      if (current.indexOf(value) >= 0) current = current.filter(function(v){ return v !== value; });
      else current.push(value);
    }
    state.answers[qd.id] = current;
    renderStep();
  }
  function loadingTexts(idx) {
    var v = ['Refining your range','Personalizing your estimate','Calculating your treatment options','Tailoring your next question','Updating your range'];
    return v[idx % v.length];
  }
  function advance() {
    if (state.currentStep < QUESTIONS.length - 1) {
      var next = state.currentStep + 1;
      loadInForm(next, loadingTexts(next), function(){ renderStep(); });
    }
  }
  function back() {
    if (state.currentStep === 0) return;
    var prev = state.currentStep - 1;
    loadInForm(prev, 'Going back', function(){ renderStep(); }, 700);
  }
  function startForm() {
    state.currentStep = 0;
    updateHeader();
    stepEl.innerHTML = renderLoaderHtml('Loading your estimator');
    state.transitioning = true;
    showView('form', function(){
      setTimeout(function(){
        animOut(stepEl, function(){
          renderStep();
          animIn(stepEl);
          state.transitioning = false;
        });
      }, 900);
    });
  }


  // ── Delegated events ──
  ROOT.addEventListener('click', function(e){
    var t = e.target.closest('[data-action]');
    if (!t) return;
    var action = t.getAttribute('data-action');
    switch (action) {
      case 'go-landing': showView('landing'); break;
      case 'go-intro':   showView('intro'); break;
      case 'start-form': startForm(); break;
      case 'back':       back(); break;
      case 'select-single': selectSingle(t.getAttribute('data-value')); break;
      case 'select-multi':  selectMulti(t.getAttribute('data-value'), t.hasAttribute('data-exclusive')); break;
      case 'continue':   if (!t.disabled) advance(); break;
      case 'toggle-why': {
        var pnl = t.closest('div').parentElement.querySelector('[data-why]');
        if (pnl) pnl.classList.toggle('hidden');
        break;
      }
    }
  });
})();
</script>

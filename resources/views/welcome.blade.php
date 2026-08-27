<!doctype html>
<html lang="es" class="scroll-smooth">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Control de Personal UGRH · GADC</title>
    <link href="{{ asset('images/logo-gober-i.png') }}" rel="icon" type="image/png">
    <!-- SEO: sistema interno, no indexar -->
    <meta name="robots" content="noindex, nofollow, noarchive">
    <!-- Información general -->
    <meta name="description" content="Sistema institucional de Control de Personal de la Unidad de Gestión de Recursos Humanos (UGRH) del Gobierno Autónomo Departamental de Cochabamba (GADC).">
    <meta name="author" content="Unidad de Gestión de Recursos Humanos - GADC">
    <meta name="application-name" content="Control de Personal UGRH">
    <!-- Open Graph - Vista previa al compartir el enlace -->
    <meta property="og:type" content="website">
    <meta property="og:title" content="Control de Personal UGRH · GADC">
    <meta property="og:description" content="Sistema institucional de la Unidad de Gestión de Recursos Humanos del Gobierno Autónomo Departamental de Cochabamba para la gestión y control del personal.">
    <meta property="og:image" content="{{ asset('images/logo-gober-i.png') }}">
    <meta property="og:image:alt" content="Gobierno Autónomo Departamental de Cochabamba">
    <meta property="og:locale" content="es_BO">
    <meta property="og:site_name" content="Gobierno Autónomo Departamental de Cochabamba">
    <meta property="og:url" content="{{ url()->current() }}">

    <!-- WhatsApp / Facebook / Messenger -->
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <!-- Twitter / X -->
    <meta name="twitter:card" content="summary_large_image">

    <meta name="twitter:title" content="Control de Personal UGRH · GADC">
    <meta name="twitter:description"content="Sistema institucional de la Unidad de Gestión de Recursos Humanos del Gobierno Autónomo Departamental de Cochabamba.">
    <meta name="twitter:image" content="{{ asset('images/logo-gober-i.png') }}">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,300;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        
      tailwind.config = {
        theme: {
          extend: {
            fontFamily: {
              sans: ['"Plus Jakarta Sans"', 'sans-serif'],
            },
            colors: {
              brand: {
                blue: '#4DA3FF',
                dark: '#1A1A1A',
                light: '#F7F9FC',
                accent: '#1E60AA'
              }
            }
          }
        }
      }
      
    </script>
    
    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
      body {
        background-color: #F7F9FC;
        color: #1A1A1A;
        font-family: 'Plus Jakarta Sans', sans-serif;
        overflow-x: hidden;
      }
      .bg-andean-pattern {
        background-image: radial-gradient(rgba(77, 163, 255, 0.15) 1.5px, transparent 1.5px),
          radial-gradient(rgba(26, 26, 26, 0.05) 1.5px, transparent 1.5px);
        background-size: 28px 28px;
        background-position: 0 0, 14px 14px;
      }
      .border-guarda-andina {
        background: linear-gradient(90deg, #4DA3FF 0%, #1A1A1A 35%, #4DA3FF 65%, #3d82cc 100%);
        height: 4px;
      }
      .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(77, 163, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.04);
      }
      .accordion-rip summary {
        list-style: none;
        display: flex;
        align-items: center;
        gap: 1rem;
        font-weight: 600;
        color: #1A1A1A;
        padding: 0.9rem 0;
        border-bottom: 1px solid rgba(0,0,0,0.04);
        cursor: pointer;
        transition: color 0.2s;
      }
      .accordion-rip summary::-webkit-details-marker { display: none; }
      .accordion-rip summary i {
        color: #4DA3FF;
        transition: transform 0.3s;
        font-size: 0.9rem;
      }
      .accordion-rip[open] summary i {
        transform: rotate(90deg);
      }
      .accordion-rip .content {
        padding: 0.8rem 0 1.5rem 1.8rem;
        color: #475569;
        line-height: 1.8;
      }
      .accordion-rip .content ul {
        list-style: disc;
        margin-left: 1.2rem;
      }
      .btn-primary {
        background: linear-gradient(135deg, #4DA3FF, #1E60AA);
        color: white;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 14px rgba(77, 163, 255, 0.35);
      }
      .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(77, 163, 255, 0.45);
      }
      @media print {
        body { background: white !important; color: black !important; }
        .no-print { display: none !important; }
      }
      .grid-acordeones{
        display: grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 1.5rem;
        align-items: start;
    }
    </style>
  </head>
  <body class="min-h-screen flex flex-col justify-between">

    <!-- ===== HEADER MODERNO CON CELESTE ===== -->
    <header class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-[#4DA3FF]/20 shadow-sm no-print">
      <div class="border-guarda-andina w-full"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-20">
          
          <!-- Brand -->
          <a href="#inicio" class="flex items-center gap-3 text-left">
            <div class="w-10 h-10  flex items-center justify-center rounded-lg shadow-md shrink-0">
                <img src="images/logo-gober-i.png" alt="">
            </div>
            <div>
              <span class="text-[10px] font-bold tracking-[0.25em] text-[#4DA3FF] uppercase block leading-tight">
                Gobierno Autónomo Departamental de Cochabamba
              </span>
              <span class="text-sm font-light text-gray-700 tracking-wide block">
                Unidad de Gestión de <strong class="font-bold text-[#4DA3FF]">Recursos Humanos</strong>
              </span>
            </div>
          </a>

        <a href="/login"
        class="flex items-center gap-2 px-6 py-2.5 rounded-full bg-[#4DA3FF] hover:bg-[#1E60AA] text-white text-xs font-bold tracking-widest uppercase transition-all shadow-md cursor-pointer active:scale-95">
            <i data-lucide="log-in" class="w-4 h-4"></i>
            <span>Iniciar Sesión</span>
        </a>

        </div>
      </div>
    </header>

    <main class="flex-1">

      <!-- ===== HERO ===== -->
      <section id="inicio" class="relative overflow-hidden bg-[#F7F9FC] text-[#1A1A1A] py-14 sm:py-20 border-b border-[#4DA3FF]/20">
        <div class="absolute inset-0 opacity-[0.03] pointer-events-none">
          <svg width="100%" height="100%" xmlns="http://www.w3.org/2000/svg">
            <defs>
              <pattern id="andean-grid" width="40" height="40" patternUnits="userSpaceOnUse">
                <path d="M 0 20 L 20 0 L 40 20 L 20 40 Z" fill="none" stroke="#1A1A1A" stroke-width="1"/>
              </pattern>
            </defs>
            <rect width="100%" height="100%" fill="url(#andean-grid)" />
          </svg>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            
            <div class="lg:col-span-7 space-y-6 text-left">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-[#4DA3FF] flex items-center justify-center rounded-sm rotate-45 shrink-0 shadow-sm">
                    <img src="images/logo-cbba.png" alt="Logo"
                        class="w-7 h-7 object-contain -rotate-45">
                </div>
                <div>
                  <span class="text-xs font-bold text-[#4DA3FF] uppercase tracking-[0.25em] block">
                    Gobierno Autónomo Departamental de Cochabamba
                  </span>
                  <span class="text-xs text-gray-500 font-light">
                    Unidad de Gestión de Recursos Humanos
                  </span>
                </div>
              </div>

              <h1 class="text-4xl sm:text-5xl lg:text-6xl font-light tracking-tight leading-[1.05] text-[#1A1A1A]">
                Sistema de <br />
                <span class="font-bold text-[#4DA3FF]">
                  Control de Personal
                </span>
              </h1>

              <p class="max-w-xl text-sm sm:text-base text-gray-600 leading-relaxed font-light italic border-l-2 border-[#4DA3FF] pl-6 py-1">
                "Nuestra misión es la modernización administrativa y el compromiso con la transparencia en el servicio al ciudadano conforme al Reglamento Interno de Personal (RIP)."
              </p>

              <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1 text-xs font-semibold tracking-wide text-gray-700">
                <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-white border border-[#4DA3FF]/20 shadow-sm">
                  <i data-lucide="check-circle-2" class="w-4 h-4 text-[#4DA3FF] shrink-0"></i>
                  <span>Exclusivo para Servidores Públicos</span>
                </div>
                <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-white border border-[#4DA3FF]/20 shadow-sm">
                  <i data-lucide="check-circle-2" class="w-4 h-4 text-[#4DA3FF] shrink-0"></i>
                  <span>Gestión RIP R.A. N° 519/2023</span>
                </div>
                <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-white border border-[#4DA3FF]/20 shadow-sm">
                  <i data-lucide="check-circle-2" class="w-4 h-4 text-[#4DA3FF] shrink-0"></i>
                  <span>Control de Asistencia Biométrico</span>
                </div>
                <div class="flex items-center gap-2.5 p-3 rounded-2xl bg-white border border-[#4DA3FF]/20 shadow-sm">
                  <i data-lucide="check-circle-2" class="w-4 h-4 text-[#4DA3FF] shrink-0"></i>
                  <span>Administración Oficial UGRH</span>
                </div>
              </div>

              <div class="pt-3 flex flex-col sm:flex-row items-stretch sm:items-center gap-4">
                <a href="/login" class="group flex items-center justify-center gap-4 px-10 py-4 bg-[#1A1A1A] text-white hover:bg-[#4DA3FF] hover:text-[#1A1A1A] rounded-full text-xs font-bold tracking-widest uppercase transition-all shadow-lg cursor-pointer">
                  <i data-lucide="log-in" class="w-4 h-4"></i>
                  <span>Ingresar al Control de Personal</span>
                  <i data-lucide="chevron-right" class="w-4 h-4"></i>
                </a>
              </div>
            </div>

            <div class="lg:col-span-5">
              <div class="rounded-3xl bg-white/90 backdrop-blur-md border border-[#4DA3FF]/20 p-6 sm:p-8 shadow-xl space-y-5 text-left">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                  <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-[#4DA3FF]/10 border border-[#4DA3FF]/30 flex items-center justify-center text-[#4DA3FF]">
                      <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </div>
                    <div>
                      <h3 class="text-sm font-bold text-[#1A1A1A] uppercase tracking-wider">Aviso Institucional UGRH</h3>
                      <p class="text-[11px] text-gray-500 font-light">Gestión Institucional GADC</p>
                    </div>
                  </div>
                  <span class="px-3 py-1 rounded-full bg-[#4DA3FF]/10 text-[#4DA3FF] border border-[#4DA3FF]/30 text-[10px] font-bold uppercase tracking-widest">
                    Gestión 2026
                  </span>
                </div>

                <div class="space-y-3 text-xs">
                  <div class="p-4 rounded-2xl bg-[#F7F9FC] border border-gray-200/80 space-y-1.5">
                    <div class="flex items-center gap-2 text-[#1A1A1A] font-bold uppercase tracking-wider text-[11px]">
                      <i data-lucide="book-open" class="w-4 h-4 text-[#4DA3FF]"></i>
                      <span>Marco Normativo del Personal</span>
                    </div>
                    <ul class="text-gray-600 space-y-1 pl-6 list-disc font-light text-[11px]">
                      <li>Reglamento Interno de Personal (R.A. N° 519/2023)</li>
                      <li>Estatuto del Funcionario Público (Ley N° 2027)</li>
                      <li>Manual de Organización y Funciones (MOF UGRH)</li>
                    </ul>
                  </div>

                  <div class="p-4 rounded-2xl bg-[#F7F9FC] border border-gray-200/80 space-y-1.5">
                    <div class="flex items-center justify-between text-[11px] font-bold uppercase text-[#1A1A1A]">
                      <span class="flex items-center gap-2">
                        <i data-lucide="bell" class="w-4 h-4 text-[#4DA3FF]"></i>
                        <span>Jornada y Tolerancia Oficial</span>
                      </span>
                      <span class="text-[#4DA3FF] font-mono">Art. 15 y 22 RIP</span>
                    </div>
                    <div class="flex justify-between font-mono text-[#1A1A1A] text-xs font-bold pt-0.5">
                      <span>08:00 - 12:00</span>
                      <span>14:30 - 18:30</span>
                    </div>
                    <p class="text-[11px] text-gray-500 font-light">
                      Tolerancia de 5 minutos al ingreso en ambos turnos.
                    </p>
                  </div>

                  <div class="p-4 rounded-2xl bg-[#4DA3FF]/5 border border-[#4DA3FF]/20 space-y-1">
                    <div class="flex items-center justify-between text-xs font-bold text-[#1A1A1A]">
                      <span class="uppercase tracking-wider flex items-center gap-1.5 text-[#4DA3FF]">
                        <i data-lucide="phone-call" class="w-3.5 h-3.5"></i>
                        Atención  UGRH
                      </span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-gray-600 font-light">
                      Edificio Central GADC EX CORDECO • 
                      <br />
                      <strong class="text-[#1A1A1A] font-mono font-bold">Central: 165</strong>
                    </p>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- ===== FUNCIONES UGRH (se mantiene) ===== -->
      <section id="funciones" class="py-16 sm:py-20 bg-white border-b border-[#4DA3FF]/20 text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
          <div class="text-center space-y-3 max-w-3xl mx-auto">
            <span class="text-[10px] font-bold tracking-[0.3em] text-[#4DA3FF] uppercase block">
              Gestión Integral del Personal
            </span>
            <h2 class="text-3xl sm:text-4xl font-light text-[#1A1A1A] tracking-tight">
              Áreas y Funciones de la <span class="font-bold text-[#4DA3FF]">Unidad UGRH</span>
            </h2>
            <p class="text-gray-600 text-sm sm:text-base leading-relaxed font-light">
              Atribuciones encomendadas para el control técnico, administrativo y procedimental de los servidores públicos de la Gobernación de Cochabamba.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-[#4DA3FF]/20 shadow-sm hover:border-[#4DA3FF] transition-all space-y-3">
              <div class="w-10 h-10 rounded-2xl bg-[#1A1A1A] text-[#4DA3FF] flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
              </div>
              <h3 class="text-base font-bold text-[#1A1A1A]">Control de Asistencia</h3>
              <p class="text-xs text-gray-600 leading-relaxed font-light">
                Monitoreo biométrico de marcaciones de ingreso y salida, registro de atrasos, licencias, omisiones y comisiones oficiales.
              </p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-[#4DA3FF]/20 shadow-sm hover:border-[#4DA3FF] transition-all space-y-3">
              <div class="w-10 h-10 rounded-2xl bg-[#1A1A1A] text-[#4DA3FF] flex items-center justify-center">
                <i data-lucide="credit-card" class="w-5 h-5"></i>
              </div>
              <h3 class="text-base font-bold text-[#1A1A1A]">Planillas y CAS</h3>
              <p class="text-xs text-gray-600 leading-relaxed font-light">
                Elaboración de sueldos, cómputo de la Calificación de Años de Servicio (CAS), subsidios familiares y descuentos de ley.
              </p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-[#4DA3FF]/20 shadow-sm hover:border-[#4DA3FF] transition-all space-y-3">
              <div class="w-10 h-10 rounded-2xl bg-[#1A1A1A] text-[#4DA3FF] flex items-center justify-center">
                <i data-lucide="file-check-2" class="w-5 h-5"></i>
              </div>
              <h3 class="text-base font-bold text-[#1A1A1A]">Licencias y Permisos</h3>
              <p class="text-xs text-gray-600 leading-relaxed font-light">
                Procesamiento e inspección de solicitudes oficiales de permiso, licencias con goce y programación anual de vacaciones.
              </p>
            </div>

            <div class="bg-white p-6 rounded-3xl border border-[#4DA3FF]/20 shadow-sm hover:border-[#4DA3FF] transition-all space-y-3">
              <div class="w-10 h-10 rounded-2xl bg-[#1A1A1A] text-[#4DA3FF] flex items-center justify-center">
                <i data-lucide="folder-git-2" class="w-5 h-5"></i>
              </div>
              <h3 class="text-base font-bold text-[#1A1A1A]">Files Personales</h3>
              <p class="text-xs text-gray-600 leading-relaxed font-light">
                Custodia y actualización periódica de carpetas de antecedentes, títulos, declaraciones juradas e incompatibilidades.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- ===== NORMATIVA RIP – AMPLIADA ===== -->
      <section id="normativa" class="py-16 sm:py-20 bg-[#F7F9FC] border-b border-[#4DA3FF]/20 text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
          
          <div class="text-center space-y-3 max-w-3xl mx-auto">
            <span class="text-[10px] font-bold tracking-[0.3em] text-[#4DA3FF] uppercase block">
              Reglamento Interno de Personal
            </span>
            <h2 class="text-3xl sm:text-4xl font-light text-[#1A1A1A] tracking-tight">
              Compendio Normativo <span class="font-bold text-[#4DA3FF]">(RIP)</span>
            </h2>
            <p class="text-gray-600 text-sm sm:text-base leading-relaxed font-light">
              Resumen de los principales artículos que rigen la relación laboral entre el GADC y sus servidores públicos, conforme a la R.A. N° 519/2023.
            </p>
          </div>

          <!-- Base legal -->
          <div class="p-6 sm:p-8 rounded-3xl bg-[#1A1A1A] text-white border border-[#4DA3FF]/30 space-y-4 shadow-xl">
            <div class="flex items-center gap-3 flex-wrap">
              <span class="px-3 py-1 rounded-full bg-[#4DA3FF]/20 text-[#4DA3FF] border border-[#4DA3FF]/40 text-xs font-mono font-bold">
                Resolución Administrativa N° 519/2023
              </span>
              <span class="text-xs text-gray-400 font-light">Aprobación Oficial Vigente GADC</span>
            </div>
            <h3 class="text-xl sm:text-2xl font-light">
              Marco Legal del <span class="font-bold text-[#4DA3FF]">Sistema de Administración de Personal</span>
            </h3>
            <p class="text-xs sm:text-sm text-gray-300 leading-relaxed font-light">
              El Reglamento Interno de Personal constituye el instrumento normativo técnico y legal para la administración ordenada del talento humano, asegurando el óptimo funcionamiento de las dependencias departamentales, en concordancia con la Constitución Política del Estado, la Ley N° 1178, la Ley N° 2027 y el D.S. N° 26115 (NB-SAP).
            </p>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs text-gray-300">
              <div><span class="text-[#4DA3FF] font-bold">●</span> Derechos (Art. 7)</div>
              <div><span class="text-[#4DA3FF] font-bold">●</span> Deberes (Art. 11)</div>
              <div><span class="text-[#4DA3FF] font-bold">●</span> Prohibiciones (Art. 13)</div>
              <div><span class="text-[#4DA3FF] font-bold">●</span> Incompatibilidades (Art. 14)</div>
            </div>
          </div>

          <!-- Acordeones del RIP -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">
            <!-- 1. Jornada y Asistencia -->
            <details class="accordion-rip glass-card rounded-xl p-5 border border-white/50">
              <summary><i data-lucide="chevron-right" class="w-4 h-4"></i> <span>Jornada Laboral y Control de Asistencia</span></summary>
              <div class="content">
                <ul>
                  <li><strong>Art. 15:</strong> Jornada de 8 horas discontinuas de lunes a viernes: 08:00–12:00 / 14:30–18:30.</li>
                  <li><strong>Art. 16:</strong> Control de asistencia mediante marcación biométrica al ingreso y salida.</li>
                  <li><strong>Art. 22:</strong> Tolerancia de 5 minutos posteriores a la hora de ingreso (no compensable).</li>
                  <li><strong>Art. 45:</strong> Atrasos acumulados: 31–45 min = ½ día de remuneración; 46–60 min = 1 día; 61–90 min = 2 días; 91–120 min = 3 días; 121+ (primera vez) = 4 días.</li>
                  <li><strong>Art. 45.II:</strong> Inasistencia injustificada: ½ día = 1 día de remuneración; 1 día = 2 días de remuneración.</li>
                  <li><strong>Art. 45.III:</strong> Omisión de registro: 1ª vez = ½ día; 2ª vez = 1 día.</li>
                </ul>
                <p class="mt-2 text-xs text-gray-400">Arts. 15, 16, 22, 45 del RIP.</p>
              </div>
            </details>

            <!-- 2. Permisos Oficiales -->
            <details class="accordion-rip glass-card rounded-xl p-5 border border-white/50">
              <summary><i data-lucide="chevron-right" class="w-4 h-4"></i> <span>Permisos Oficiales</span></summary>
              <div class="content">
                <ul>
                  <li><strong>Art. 17:</strong> Clasificación: Comisión, permiso por horas, asueto, tolerancia, otros.</li>
                  <li><strong>Art. 18 (Comisión):</strong> Dentro de la institución hasta 3 meses; traslado nacional hasta 2 meses; exterior hasta 1 mes. Con goce de haberes y viáticos.</li>
                  <li><strong>Art. 19 (Permiso por horas):</strong> Actividades inherentes al cargo en el día, con registro de salidas/ingresos.</li>
                  <li><strong>Art. 21 (Asueto):</strong> Día del Padre/Madre (½ día), cumpleaños (½ día), fallecimiento de parientes (hasta 3 días hábiles), compensación laboral por horas extras ≥4h = ½ día, ≥8h = 1 día.</li>
                  <li><strong>Art. 22 (Tolerancia):</strong> Compensación laboral por actividades en horarios distintos; 5 min diarios de tolerancia sin compensación.</li>
                </ul>
                <p class="mt-2 text-xs text-gray-400">Arts. 17–24 del RIP, R.M. N° 1111.</p>
              </div>
            </details>

            <!-- 3. Permisos Personales -->
            <details class="accordion-rip glass-card rounded-xl p-5 border border-white/50">
              <summary><i data-lucide="chevron-right" class="w-4 h-4"></i> <span>Permisos Personales</span></summary>
              <div class="content">
                <ul>
                  <li><strong>Art. 25 (Con goce):</strong> Hasta 30 minutos fraccionados, con autorización del jefe. Compensación con horas de trabajo o vacaciones.</li>
                  <li><strong>Art. 27 (Sin goce):</strong> Por atención médica (hasta 3 meses en país, 4 meses exterior), estudios (hasta 3 meses), actividades culturales/deportivas (hasta 1 mes), otros asuntos (15 días/año).</li>
                  <li><strong>Art. 28:</strong> Presentación de descargos en plazo máximo de 1 mes desde la reincorporación.</li>
                  <li><strong>Art. 30:</strong> Deducción del líquido pagable proporcional a los días no trabajados; obligaciones patronales se computan íntegramente.</li>
                </ul>
                <p class="mt-2 text-xs text-gray-400">Arts. 25–30 del RIP, R.M. N° 1111.</p>
              </div>
            </details>

            <!-- 4. Licencias -->
            <details class="accordion-rip glass-card rounded-xl p-5 border border-white/50">
              <summary><i data-lucide="chevron-right" class="w-4 h-4"></i> <span>Licencias</span></summary>
              <div class="content">
                <ul>
                  <li><strong>Art. 31:</strong> Tramitación mediante Formulario de Licencia, con comunicación a UGRH.</li>
                  <li><strong>Art. 32:</strong> Licencias establecidas en norma específica (Ley 2027, D.S. 25749, D.S. 1212, D.S. 3462).</li>
                  <li><strong>Ley 2027, Art. 48:</strong> Licencias con 100% de remuneración sin cargo a vacaciones: capacitación, matrimonio (3 días hábiles), fallecimiento de padres/cónyuge/hermanos/hijos (3 días hábiles).</li>
                  <li><strong>D.S. 1212:</strong> Licencia por paternidad de 3 días hábiles por nacimiento de hijo.</li>
                  <li><strong>D.S. 3462:</strong> Licencia especial para madres/padres/tutores de niños con estado crítico de salud (hasta 5 días/mes por cáncer, 3 días prequirúrgicos + 1 día quirúrgico + 10 días postrasplante, etc.).</li>
                </ul>
                <p class="mt-2 text-xs text-gray-400">Arts. 31–32, Ley 2027, D.S. 25749, D.S. 1212, D.S. 3462.</p>
              </div>
            </details>

            <!-- 5. Vacaciones -->
            <details class="accordion-rip glass-card rounded-xl p-5 border border-white/50">
              <summary><i data-lucide="chevron-right" class="w-4 h-4"></i> <span>Vacaciones</span></summary>
              <div class="content">
                <ul>
                  <li><strong>Art. 33:</strong> Régimen de vacaciones: días completos o medios días (no fraccionados por horas), en días hábiles.</li>
                  <li><strong>Escala (Ley 2027, Art. 49):</strong> 1–5 años: 15 días; 5–10 años: 20 días; 10+ años: 30 días.</li>
                  <li><strong>Art. 33.II.b:</strong> Las vacaciones adquiridas deben tomarse en la gestión inmediata posterior, con programación anual.</li>
                  <li><strong>Art. 33.II.d:</strong> Reprogramación excepcional con visto bueno del jefe.</li>
                  <li><strong>Art. 35:</strong> Para acceder a periodos mayores, presentar Certificado de Calificación de Años de Servicio (CAS).</li>
                </ul>
                <p class="mt-2 text-xs text-gray-400">Arts. 33–35, Ley 2027 Art. 49.</p>
              </div>
            </details>

            <!-- 6. Régimen Disciplinario -->
            <details class="accordion-rip glass-card rounded-xl p-5 border border-white/50">
              <summary><i data-lucide="chevron-right" class="w-4 h-4"></i> <span>Régimen Disciplinario y Faltas</span></summary>
              <div class="content">
                <ul>
                  <li><strong>Art. 41:</strong> Faltas Leves, Graves y Gravísimas, con sanciones administrativas y/o económicas.</li>
                  <li><strong>Art. 43 (Leves con amonestación verbal):</strong> Negligencia, trato descortés, no iniciar labores, promover actividades ajenas.</li>
                  <li><strong>Art. 44 (Leves con amonestación escrita):</strong> Reincidencia, no asistir a eventos, incumplir instrucciones, faltar el respeto.</li>
                  <li><strong>Art. 46 (Graves con sanción económica):</strong> Reincidencia (3ª vez), presentarse en estado de ebriedad, provocar actividades irregulares, entregar información confidencial. Sanción: 2 días de remuneración.</li>
                  <li><strong>Art. 47 (Gravísimas con proceso interno):</strong> Reincidencia (4ª vez), hostigamiento/acoso, vulnerar controles. Sanción: 20% de la remuneración (6 días) o destitución según reincidencia.</li>
                  <li><strong>Art. 48:</strong> Inasistencia injustificada por 3 días consecutivos o 6 discontinuos en un mes = abandono de funciones (retiro).</li>
                </ul>
                <p class="mt-2 text-xs text-gray-400">Arts. 39–49 del RIP.</p>
              </div>
            </details>

            <!-- 7. Derechos y Deberes -->
            <details class="accordion-rip glass-card rounded-xl p-5 border border-white/50">
              <summary><i data-lucide="chevron-right" class="w-4 h-4"></i> <span>Derechos, Deberes y Prohibiciones</span></summary>
              <div class="content">
                <ul>
                  <li><strong>Art. 10 (Derechos):</strong> Remuneración justa, vacaciones, licencias, permisos, estabilidad (funcionarios de carrera), capacitación, impugnación, etc.</li>
                  <li><strong>Art. 11 (Deberes):</strong> Cumplir jornada, atender con calidad, abstenerse de alcohol/drogas, evitar hostigamiento, custodiar bienes.</li>
                  <li><strong>Art. 13 (Prohibiciones):</strong> Ejercer más de una actividad remunerada (salvo excepciones), usar bienes institucionales para fines particulares, recibir dádivas.</li>
                  <li><strong>Art. 14 (Incompatibilidades):</strong> Parentesco hasta segundo grado de consanguinidad o afinidad en la misma entidad.</li>
                </ul>
                <p class="mt-2 text-xs text-gray-400">Arts. 10–14 del RIP.</p>
              </div>
            </details>

            <!-- 8. Incentivos y Sanciones -->
            <details class="accordion-rip glass-card rounded-xl p-5 border border-white/50">
              <summary><i data-lucide="chevron-right" class="w-4 h-4"></i> <span>Incentivos y Sanciones Económicas</span></summary>
              <div class="content">
                <ul>
                  <li><strong>Art. 40 (Incentivos):</strong> Sociales y laborales, según criterios de calidad, calidez y oportunidad.</li>
                  <li><strong>Art. 45 (Sanciones por atrasos):</strong> Escala progresiva de descuentos en la remuneración mensual.</li>
                  <li><strong>Art. 46 (Faltas graves):</strong> Sanción de 2 días de remuneración por reincidencia o faltas específicas.</li>
                  <li><strong>Art. 47 (Faltas gravísimas):</strong> Sanción del 20% (6 días) o destitución, previo proceso administrativo interno.</li>
                  <li><strong>Art. 51:</strong> Notificaciones y descargos: el afectado tiene 48 horas para presentar representación o descargos.</li>
                </ul>
                <p class="mt-2 text-xs text-gray-400">Arts. 40–47, 51 del RIP.</p>
              </div>
            </details>
          </div>

          <div class="text-center mt-6">
            <a href="#" class="text-[#4DA3FF] hover:underline text-sm font-medium">Consultar el documento completo del RIP (PDF) →</a>
          </div>
        </div>
      </section>

      <!-- ===== DIRECTORIO Y FAQ (se mantiene) ===== -->
      <section id="directorio" class="py-16 sm:py-20 bg-white border-b border-[#4DA3FF]/20 text-left">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
          
          <div class="text-center space-y-3 max-w-3xl mx-auto">
            <span class="text-[10px] font-bold tracking-[0.3em] text-[#4DA3FF] uppercase block">
              Atención al Servidor Público
            </span>
            <h2 class="text-3xl sm:text-4xl font-light text-[#1A1A1A] tracking-tight">
              Directorio UGRH <span class="font-bold text-[#4DA3FF]">y Preguntas Frecuentes</span>
            </h2>
            <p class="text-gray-600 text-sm sm:text-base leading-relaxed font-light">
              La Unidad de Gestión de Recursos Humanos brinda asistencia personalizada a los servidores públicos del Gobierno Autónomo Departamental de Cochabamba.
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-8 rounded-3xl border border-[#4DA3FF]/20 shadow-sm hover:border-[#4DA3FF] transition-all space-y-4">
              <div class="w-12 h-12 rounded-2xl bg-[#1A1A1A] text-[#4DA3FF] flex items-center justify-center">
                <i data-lucide="map-pin" class="w-6 h-6"></i>
              </div>
              <h3 class="text-lg font-bold text-[#1A1A1A]">Ubicación Física</h3>
              <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-light">
                Edificio Central de la Gobernación de Cochabamba, Plaza 14 de Septiembre (Lado Este).
                <br />
                Ventanilla Única UGRH - Planta Baja.
              </p>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-[#4DA3FF]/20 shadow-sm hover:border-[#4DA3FF] transition-all space-y-4">
              <div class="w-12 h-12 rounded-2xl bg-[#1A1A1A] text-[#4DA3FF] flex items-center justify-center">
                <i data-lucide="phone-call" class="w-6 h-6"></i>
              </div>
              <h3 class="text-lg font-bold text-[#1A1A1A]">Teléfonos y Anexos</h3>
              <div class="text-xs sm:text-sm text-gray-600 space-y-1 font-mono">
                <p>Central: (interno) 165</p>
                <p>Anexo Control de Asistencia: 1120</p>
                <p>Anexo Planillas y Permisos: 1122</p>
              </div>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-[#4DA3FF]/20 shadow-sm hover:border-[#4DA3FF] transition-all space-y-4">
              <div class="w-12 h-12 rounded-2xl bg-[#1A1A1A] text-[#4DA3FF] flex items-center justify-center">
                <i data-lucide="clock" class="w-6 h-6"></i>
              </div>
              <h3 class="text-lg font-bold text-[#1A1A1A]">Horarios de Atención</h3>
              <p class="text-xs sm:text-sm text-gray-600 leading-relaxed font-mono">
                Lunes a Viernes (Jornada Discontinua):
                <br />
                • Mañana: 08:00 a 12:00
                <br />
                • Tarde: 14:30 a 18:30
              </p>
            </div>
          </div>

          <!-- FAQ Accordion -->
          <div class="bg-[#F7F9FC] p-6 sm:p-10 rounded-3xl border border-[#4DA3FF]/20 shadow-sm space-y-6">
            <div class="flex items-center gap-3 border-b border-gray-200 pb-4">
              <i data-lucide="help-circle" class="w-6 h-6 text-[#4DA3FF]"></i>
              <div>
                <h3 class="text-lg font-bold text-[#1A1A1A]">Preguntas Frecuentes sobre el Control de Personal (FAQ)</h3>
                <p class="text-xs text-gray-500 font-light mt-0.5">Consultas habituales sobre marcaciones, tolerancias, licencias y solicitudes.</p>
              </div>
            </div>

            <div class="space-y-3">
              <div class="rounded-2xl border border-gray-200 overflow-hidden">
                <button onclick="toggleFaq('faq-1')" class="w-full text-left p-4 bg-white hover:bg-gray-100 flex items-center justify-between gap-4 font-bold text-[#1A1A1A] text-xs sm:text-sm cursor-pointer">
                  <span class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-[#4DA3FF]/10 text-[#4DA3FF] border border-[#4DA3FF]/30 text-[10px] font-mono uppercase">Tolerancia</span>
                    <span>¿Cuál es la tolerancia establecida para el registro de ingreso?</span>
                  </span>
                  <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 shrink-0"></i>
                </button>
                <div id="faq-1" class="p-5 bg-white text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100 font-light hidden">
                  Conforme al Artículo 22 del RIP (R.A. 519/2023), se establece una tolerancia máxima de cinco (5) minutos posteriores a la hora fijada para el ingreso tanto en el turno mañana (hasta 08:05) como en el turno tarde (hasta 14:35).
                </div>
              </div>

              <div class="rounded-2xl border border-gray-200 overflow-hidden">
                <button onclick="toggleFaq('faq-2')" class="w-full text-left p-4 bg-white hover:bg-gray-100 flex items-center justify-between gap-4 font-bold text-[#1A1A1A] text-xs sm:text-sm cursor-pointer">
                  <span class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-[#4DA3FF]/10 text-[#4DA3FF] border border-[#4DA3FF]/30 text-[10px] font-mono uppercase">Permisos</span>
                    <span>¿Con qué anticipación debo presentar el Formulario UGRH-01 de permiso?</span>
                  </span>
                  <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 shrink-0"></i>
                </button>
                <div id="faq-2" class="p-5 bg-white text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100 font-light hidden">
                  Los permisos particulares u oficiales deben ser autorizados mediante el Formulario UGRH-01 firmado por el Jefe Inmediato Superior e ingresados a ventanilla UGRH con al menos 24 horas de anticipación.
                </div>
              </div>

              <div class="rounded-2xl border border-gray-200 overflow-hidden">
                <button onclick="toggleFaq('faq-3')" class="w-full text-left p-4 bg-white hover:bg-gray-100 flex items-center justify-between gap-4 font-bold text-[#1A1A1A] text-xs sm:text-sm cursor-pointer">
                  <span class="flex items-center gap-2">
                    <span class="px-3 py-1 rounded-full bg-[#4DA3FF]/10 text-[#4DA3FF] border border-[#4DA3FF]/30 text-[10px] font-mono uppercase">Vacaciones</span>
                    <span>¿Cómo se realiza el cómputo de vacaciones anuales según la CAS?</span>
                  </span>
                  <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 shrink-0"></i>
                </button>
                <div id="faq-3" class="p-5 bg-white text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100 font-light hidden">
                  De acuerdo con el Estatuto del Funcionario Público (Ley 2027) y el RIP: De 1 a 5 años corresponden 15 días hábiles; de 5 a 10 años corresponden 20 días hábiles; y de 10 años en adelante corresponden 30 días hábiles.
                </div>
              </div>
            </div>
          </div>

        </div>
      </section>

    </main>

    <!-- ===== FOOTER CON CELESTE INSTITUCIONAL ===== -->
    <footer class="bg-[#1A1A1A] text-gray-300 border-t border-[#4DA3FF]/20 no-print relative overflow-hidden">
      <div class="border-guarda-andina w-full"></div>
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 text-left">
          
          <div class="space-y-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 bg-[#4DA3FF] flex items-center justify-center rounded-lg">
                <i data-lucide="building-2" class="w-5 h-5 text-[#1A1A1A]"></i>
              </div>
              <span class="font-bold text-white text-sm">GADC • UGRH</span>
            </div>
            <p class="text-xs text-gray-400 leading-relaxed font-light">
              Plataforma Institucional Oficial de la Unidad de Gestión de Recursos Humanos para el control administrativo del personal del Gobierno Autónomo Departamental de Cochabamba.
            </p>
          </div>

          <div>
            <h4 class="text-xs font-bold text-white uppercase tracking-[0.2em] mb-4 border-b border-[#4DA3FF]/20 pb-2">
              Secciones
            </h4>
            <ul class="space-y-2 text-xs font-light">
              <li><a href="#inicio" class="hover:text-[#4DA3FF] transition-colors">Inicio Portal</a></li>
              <li><a href="#funciones" class="hover:text-[#4DA3FF] transition-colors">Funciones UGRH</a></li>
              <li><a href="#normativa" class="hover:text-[#4DA3FF] transition-colors">Normativa RIP</a></li>
              <li><a href="#directorio" class="hover:text-[#4DA3FF] transition-colors">Directorio & FAQ</a></li>
            </ul>
          </div>


          <div>
            <h4 class="text-xs font-bold text-white uppercase tracking-[0.2em] mb-4 border-b border-[#4DA3FF]/20 pb-2">
              Contacto UGRH
            </h4>
            <div class="space-y-2 text-xs text-gray-300 font-light">
              <p>Edificio Central GADC - EX CORDECO, Av. Aroma y junin, Cochabamba.</p>
              <p class="font-mono text-[#4DA3FF]">(Interno) 165 • Anexo 1120</p>
            </div>
          </div>

        </div>

        <div class="pt-8 mt-8 border-t border-gray-800 flex flex-col md:flex-row items-center justify-between gap-4 text-xs text-gray-400 font-light">
          <div>
            © 2026 Gobierno Autónomo Departamental de Cochabamba. Todos los derechos reservados.
          </div>
          <div class="text-[11px] font-semibold text-[#4DA3FF] uppercase tracking-wider">
            Unidad de Gestión de Recursos Humanos • Cochabamba, Bolivia
          </div>
        </div>
      </div>
    </footer>

    <!-- ===== MODALES (placeholder) ===== -->
    <script>
      function toggleModal(id) {
        alert('Función modal para: ' + id);
        // Aquí puedes implementar tu lógica de modales
      }
      function toggleFaq(id) {
        const el = document.getElementById(id);
        if (el) el.classList.toggle('hidden');
      }
    </script>
    <script>
      lucide.createIcons();
    </script>
  </body>
</html>
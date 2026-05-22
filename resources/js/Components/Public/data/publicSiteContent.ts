import { publicImage, type PublicImageKey } from '@/Components/Public/data/publicImages';

/** Contenido demo para sitio público institucional. */

export type BreadcrumbItem = { label: string; href?: string };

export type LevelKey = 'inicial' | 'primaria' | 'secundaria';

export const levelContent: Record<
    LevelKey,
    {
        title: string;
        grades: string;
        ages: string;
        tagline: string;
        proposal: string;
        methodology: string[];
        profile: string;
        activities: string[];
    }
> = {
    inicial: {
        title: 'Nivel Inicial',
        grades: '3, 4 y 5 años',
        ages: 'De 3 a 5 años',
        tagline: 'Primeros pasos con afecto, juego y bases sólidas.',
        proposal:
            'Estimulación temprana, autonomía progresiva y desarrollo socioemocional en un ambiente seguro y luminoso.',
        methodology: [
            'Aprendizaje basado en juego y rutinas claras',
            'Inglés por inmersión gradual',
            'Acompañamiento personalizado por aula',
        ],
        profile:
            'Niños y niñas curiosos, expresivos y en formación de hábitos de convivencia y comunicación.',
        activities: ['Psicomotricidad', 'Arte y música', 'Cuentacuentos', 'Juego libre guiado'],
    },
    primaria: {
        title: 'Nivel Primaria',
        grades: '1.º al 6.º grado',
        ages: 'De 6 a 11 años',
        tagline: 'Lectoescritura, pensamiento lógico y valores en acción.',
        proposal:
            'Competencias fundamentales, proyectos STEAM y formación ciudadana con seguimiento cercano por tutoría.',
        methodology: [
            'Aula invertida en ciencias y matemática',
            'Proyectos interdisciplinarios trimestrales',
            'Evaluación formativa y retroalimentación',
        ],
        profile:
            'Estudiantes responsables, colaborativos y con hábitos de estudio que preparan la transición a secundaria.',
        activities: ['Olimpiadas escolares', 'Club de lectura', 'Robótica básica', 'Deporte formativo'],
    },
    secundaria: {
        title: 'Nivel Secundaria',
        grades: '1.º al 5.º grado',
        ages: 'De 12 a 16 años',
        tagline: 'Excelencia académica y proyección al futuro.',
        proposal:
            'Pensamiento crítico, orientación vocacional y preparación para la vida universitaria con estándares exigentes.',
        methodology: [
            'Seminarios de investigación',
            'Convenios de orientación universitaria',
            'Programa de liderazgo estudiantil',
        ],
        profile:
            'Jóvenes íntegros, autónomos y comprometidos con su entorno y su proyecto de vida.',
        activities: ['Modelo ONU', 'Emprendimiento', 'Laboratorio avanzado', 'Intercambios culturales (demo)'],
    },
};

export const demoNews = [
    {
        slug: 'apertura-proceso-admision-2026',
        title: 'Apertura del proceso de admisión 2026',
        excerpt: 'Conoce fechas, requisitos y agenda tu visita guiada al campus.',
        date: '2026-03-01',
        category: 'Admisión',
        imageKey: 'newsAdmision' as PublicImageKey,
    },
    {
        slug: 'feria-ciencias-horizonte',
        title: 'Feria de ciencias: innovación desde el aula',
        excerpt: 'Proyectos destacados de primaria y secundaria en nuestra feria anual.',
        date: '2026-02-15',
        category: 'Vida escolar',
        imageKey: 'newsFeria' as PublicImageKey,
    },
    {
        slug: 'reconocimiento-olimpiadas',
        title: 'Reconocimiento en olimpiadas regionales',
        excerpt: 'Nuestros estudiantes obtuvieron medallas en matemática y comunicación.',
        date: '2026-01-20',
        category: 'Logros',
        imageKey: 'newsOlimpiadas' as PublicImageKey,
    },
];

export function newsWithImages() {
    return demoNews.map((n) => ({ ...n, image: publicImage(n.imageKey) }));
}

export const schoolLifeEditorial = [
    {
        title: 'Talleres formativos',
        description: 'Clubes, robótica, oratoria y arte en espacios que despiertan talento.',
        imageKey: 'vidaTalleres' as PublicImageKey,
        href: 'public.vida-escolar.talleres',
    },
    {
        title: 'Deporte y bienestar',
        description: 'Equipos intercolegiales y hábitos saludables para cuerpo y mente.',
        imageKey: 'vidaDeportes' as PublicImageKey,
        href: 'public.vida-escolar',
        size: 'large' as const,
    },
    {
        title: 'Arte y cultura',
        description: 'Música, teatro y danza en muestras que celebran la creatividad.',
        imageKey: 'vidaArte' as PublicImageKey,
        href: 'public.vida-escolar.actividades',
    },
    {
        title: 'Ciencia y emprendimiento',
        description: 'Ferias, laboratorio y proyectos STEAM con pensamiento crítico.',
        imageKey: 'vidaCiencia' as PublicImageKey,
        href: 'public.vida-escolar.actividades',
    },
    {
        title: 'Liderazgo estudiantil',
        description: 'Gobierno escolar, mediación y servicio que forman ciudadanos.',
        imageKey: 'vidaLiderazgo' as PublicImageKey,
        href: 'public.vida-escolar',
        size: 'tall' as const,
    },
    {
        title: 'Eventos que unen',
        description: 'Ceremonias, aniversarios y celebraciones con identidad Horizonte.',
        imageKey: 'vidaEventos' as PublicImageKey,
        href: 'public.vida-escolar.eventos',
    },
];

export const galleryGridItems = [
    { id: '1', title: 'Campus al amanecer', category: 'Infraestructura', imageKey: 'galeria01' as PublicImageKey, span: 'md:col-span-2 md:row-span-2 min-h-[18rem]' },
    { id: '2', title: 'Biblioteca', category: 'Académico', imageKey: 'galeria02' as PublicImageKey },
    { id: '3', title: 'Educación física', category: 'Deportes', imageKey: 'galeria03' as PublicImageKey },
    { id: '4', title: 'Aula inicial', category: 'Inicial', imageKey: 'galeria04' as PublicImageKey },
    { id: '5', title: 'Laboratorio', category: 'Ciencia', imageKey: 'galeria05' as PublicImageKey, span: 'md:col-span-2 min-h-[14rem]' },
    { id: '6', title: 'Ensayo general', category: 'Arte', imageKey: 'galeria06' as PublicImageKey },
    { id: '7', title: 'Graduación', category: 'Eventos', imageKey: 'galeria07' as PublicImageKey },
    { id: '8', title: 'Trabajo en equipo', category: 'Liderazgo', imageKey: 'galeria08' as PublicImageKey },
    { id: '9', title: 'Torneo deportivo', category: 'Deportes', imageKey: 'galeria09' as PublicImageKey },
    { id: '10', title: 'Clase de música', category: 'Talleres', imageKey: 'galeria10' as PublicImageKey },
    { id: '11', title: 'Mural estudiantil', category: 'Arte', imageKey: 'galeria11' as PublicImageKey },
    { id: '12', title: 'Feria de ciencias', category: 'Ciencia', imageKey: 'galeria12' as PublicImageKey, span: 'md:col-span-2 min-h-[14rem]' },
];

export const communityTestimonials = [
    {
        quote: 'Horizonte ha sido un segundo hogar para mis hijos. Valoro la cercanía del equipo docente y la comunicación constante con las familias.',
        name: 'Sra. Carmen Vela',
        role: 'Apoderada',
        org: 'Comunidad Horizonte',
    },
    {
        quote: 'Aquí la excelencia académica se vive con calidez humana. Mis estudiantes se sienten acompañados y desafiados cada día.',
        name: 'Prof. Luis Mendoza',
        role: 'Docente de Secundaria',
        org: 'Área de Ciencias',
    },
    {
        quote: 'La formación integral y los valores institucionales se notan en cada promoción. Es un colegio en el que confiamos plenamente.',
        name: 'Diego Ramírez',
        role: 'Egresado 2024',
        org: 'Ingeniería — universidad nacional',
    },
];

export const leadershipTeam = [
    { name: 'Mg. Patricia Rojas', role: 'Directora general', area: 'Gestión institucional' },
    { name: 'Lic. Carlos Mendoza', role: 'Director académico', area: 'Currícula y calidad' },
    { name: 'Ps. Ana Torres', role: 'Coordinación de bienestar', area: 'Convivencia escolar' },
];

export function galleryWithImages() {
    return galleryGridItems.map((item) => ({
        id: item.id,
        title: item.title,
        category: item.category,
        image: publicImage(item.imageKey),
        span: item.span,
    }));
}

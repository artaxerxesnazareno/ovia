import { Head, router } from '@inertiajs/react';
import {
    BookOpen,
    Brain,
    Briefcase,
    Compass,
    Lightbulb,
    Medal,
    Search,
    Sparkles,
} from 'lucide-react';

interface AssessmentData {
    id: number;
    status: 'pending' | 'processing' | 'completed' | 'failed';
    completed_at: string | null;
    processing_time_seconds: number | null;
}

interface ProfileAnalysis {
    summary: string;
    personality_type: string;
    strengths: string[];
    areas_to_develop: string[];
    career_values: string[];
}

interface CareerPath {
    title: string;
    description: string;
    average_salary_range: string;
    market_demand: string;
    key_skills: string[];
    growth_potential: string;
}

interface CourseInfo {
    description: string;
    duration_semesters: number | null;
    duration_years_label: string;
    course_type: string;
    shifts: string[];
    vacancies_per_year: number | null;
    coordinator_name: string;
    admission_requirements: string;
    curriculum_topics: string[];
}

interface CourseRecommendation {
    course_name: string;
    course_slug: string;
    rank: number;
    compatibility_score: number;
    justification: string;
    student_strengths_for_course: string[];
    potential_challenges: string[];
    career_paths: CareerPath[];
    course: CourseInfo;
}

interface GoalItem {
    goal: string;
    timeframe: string;
    actions: string[];
    resources: string[];
    estimated_hours_week: string;
}

interface Roadmap {
    short_term: GoalItem[];
    medium_term: GoalItem[];
    long_term: GoalItem[];
    certifications_to_consider: string[];
    books_recommended: string[];
    communities_to_join: string[];
}

interface AdditionalAdvice {
    immediate_next_steps: string;
    long_term_vision: string;
    words_of_encouragement: string;
}

interface ResultsProps {
    assessment: AssessmentData;
    profileAnalysis: ProfileAnalysis;
    recommendations: CourseRecommendation[];
    roadmap: Roadmap;
    additionalAdvice: AdditionalAdvice;
}

interface DimensionScore {
    label: string;
    score: number;
}

const clampScore = (value: number) => Math.max(30, Math.min(98, value));

const deriveDimensionScores = (profile: ProfileAnalysis): DimensionScore[] => {
    const signalText = [
        profile.personality_type,
        profile.summary,
        ...profile.strengths,
        ...profile.career_values,
    ]
        .join(' ')
        .toLowerCase();

    const hasAny = (keywords: string[]) => keywords.some((keyword) => signalText.includes(keyword));
    const boost = (keywords: string[], base: number, bonus: number) => (hasAny(keywords) ? base + bonus : base);

    return [
        {
            label: 'Logica e Matematica',
            score: clampScore(boost(['logica', 'analit', 'dados', 'matemat'], 70, 20)),
        },
        {
            label: 'Criatividade',
            score: clampScore(boost(['criativ', 'inov', 'arte', 'design'], 60, 20)),
        },
        {
            label: 'Comunicacao',
            score: clampScore(boost(['comunic', 'apresent', 'lingua', 'explicar'], 58, 18)),
        },
        {
            label: 'Lideranca',
            score: clampScore(boost(['lider', 'decis', 'coorden'], 55, 18)),
        },
        {
            label: 'Trabalho em Equipe',
            score: clampScore(boost(['equipe', 'colabor', 'grupo'], 62, 20)),
        },
        {
            label: 'Tecnologia',
            score: clampScore(boost(['tecnolog', 'software', 'program', 'sistema'], 68, 22)),
        },
    ];
};

const scoreBadge = (score: number) => {
    if (score >= 90) return { text: 'Alta Compatibilidade', accent: 'bg-emerald-500', chip: 'bg-emerald-50 text-emerald-700' };
    if (score >= 80) return { text: 'Boa Compatibilidade', accent: 'bg-blue-500', chip: 'bg-blue-50 text-blue-700' };
    return { text: 'Media Compatibilidade', accent: 'bg-amber-400', chip: 'bg-amber-50 text-amber-700' };
};

const profileTagClass = 'rounded border border-slate-200 bg-white px-2 py-1 text-xs text-slate-700';

function ProfileRadar({ dimensions }: { dimensions: DimensionScore[] }) {
    const size = 360;
    const center = size / 2;
    const maxRadius = 120;
    const levels = [0.2, 0.4, 0.6, 0.8, 1];
    const step = (Math.PI * 2) / dimensions.length;

    const getPoint = (index: number, normalizedRadius: number) => {
        const angle = -Math.PI / 2 + step * index;
        return {
            x: center + Math.cos(angle) * maxRadius * normalizedRadius,
            y: center + Math.sin(angle) * maxRadius * normalizedRadius,
        };
    };

    const toPath = (normalizedRadius: number) =>
        dimensions
            .map((_, index) => {
                const point = getPoint(index, normalizedRadius);
                return `${point.x},${point.y}`;
            })
            .join(' ');

    const polygon = dimensions
        .map((dimension, index) => {
            const point = getPoint(index, Math.max(0.1, Math.min(1, dimension.score / 100)));
            return `${point.x},${point.y}`;
        })
        .join(' ');

    return (
        <div className="relative mx-auto h-72 w-full max-w-xl">
            <svg viewBox={`0 0 ${size} ${size}`} className="h-full w-full">
                {levels.map((level) => (
                    <polygon
                        key={level}
                        points={toPath(level)}
                        fill="none"
                        stroke="rgba(148, 163, 184, 0.45)"
                        strokeWidth={1}
                    />
                ))}

                {dimensions.map((_, index) => {
                    const point = getPoint(index, 1);
                    return (
                        <line
                            key={`axis-${index}`}
                            x1={center}
                            y1={center}
                            x2={point.x}
                            y2={point.y}
                            stroke="rgba(148, 163, 184, 0.35)"
                            strokeWidth={1}
                        />
                    );
                })}

                <polygon points={polygon} fill="rgba(59, 130, 246, 0.24)" stroke="#3b82f6" strokeWidth={2} />

                {dimensions.map((dimension, index) => {
                    const point = getPoint(index, Math.max(0.1, Math.min(1, dimension.score / 100)));
                    return <circle key={`dot-${dimension.label}`} cx={point.x} cy={point.y} r={4} fill="#3b82f6" />;
                })}

                {dimensions.map((dimension, index) => {
                    const point = getPoint(index, 1.14);
                    return (
                        <text
                            key={`label-${dimension.label}`}
                            x={point.x}
                            y={point.y}
                            textAnchor="middle"
                            dominantBaseline="middle"
                            fontSize={11}
                            fill="#64748b"
                            className="font-semibold"
                        >
                            {dimension.label}
                        </text>
                    );
                })}
            </svg>
        </div>
    );
}

export default function Results({ assessment, profileAnalysis, recommendations, roadmap, additionalAdvice }: ResultsProps) {
    const dimensions = deriveDimensionScores(profileAnalysis);
    const topRecommendation = recommendations[0] || null;

    return (
        <>
            <Head title="Resultados IMETRO" />

            <div className="min-h-screen bg-slate-100 text-slate-900">
                <header className="sticky top-0 z-50 border-b border-slate-200 bg-white/95 backdrop-blur">
                    <div className="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                        <button type="button" className="flex items-center gap-2" onClick={() => router.visit('/')}>
                            <div className="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-blue-500 to-indigo-500 text-sm font-bold text-white">
                                I
                            </div>
                            <span className="bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-2xl font-extrabold text-transparent">
                                IMETRO
                            </span>
                        </button>

                        <nav className="hidden items-center gap-8 text-sm md:flex">
                            <button type="button" className="text-slate-500 hover:text-blue-600" onClick={() => router.visit('/app-dashboard')}>
                                Inicio
                            </button>
                            <span className="rounded-md bg-blue-50 px-3 py-2 font-bold text-blue-600">Resultados</span>
                            <button type="button" className="text-slate-500 hover:text-blue-600" onClick={() => router.visit(`/assessment/${assessment.id}/results/course/1`)}>
                                Roadmap
                            </button>
                            <button type="button" className="text-slate-500 hover:text-blue-600">
                                Comparar Cursos
                            </button>
                        </nav>

                        <div className="flex items-center gap-3">
                            <button
                                type="button"
                                className="rounded-full border border-slate-200 p-2 text-slate-500 transition hover:bg-slate-50"
                                onClick={() => router.visit('/assessment/start')}
                            >
                                <Search className="h-4 w-4" />
                            </button>
                            <div className="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-blue-400 to-purple-500 text-xs font-bold text-white">
                                JS
                            </div>
                        </div>
                    </div>
                </header>

                <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <section className="mb-10 text-center">
                        <span className="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700">
                            <Sparkles className="h-3.5 w-3.5" />
                            Analise concluida
                        </span>
                        <h1 className="mt-4 text-3xl font-extrabold text-slate-900 md:text-4xl">Seus Resultados de Orientacao</h1>
                        <p className="mx-auto mt-3 max-w-3xl text-slate-600">
                            Baseado em nossa IA, analisamos seu perfil e encontramos as melhores conexoes para o seu futuro no IMETRO.
                        </p>
                    </section>

                    <section className="mb-12 grid grid-cols-1 gap-8 lg:grid-cols-3">
                        <article className="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg lg:col-span-1">
                            <div className="flex flex-col items-center text-center">
                                <div className="mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-gradient-to-br from-blue-100 to-indigo-100">
                                    <Brain className="h-10 w-10 text-blue-600" />
                                </div>
                                <h2 className="text-xl font-bold text-slate-900">{profileAnalysis.personality_type || 'Perfil em definicao'}</h2>
                                <p className="mt-1 text-sm text-slate-500">Seu arquetipo profissional</p>
                            </div>

                            <div className="mt-6 rounded-xl bg-slate-50 p-4">
                                <div className="mb-2 flex items-center gap-2">
                                    <Lightbulb className="h-4 w-4 text-blue-600" />
                                    <span className="text-sm font-semibold text-slate-700">Pontos Fortes</span>
                                </div>
                                <div className="flex flex-wrap gap-2">
                                    {profileAnalysis.strengths.slice(0, 6).map((strength, index) => (
                                        <span key={`${strength}-${index}`} className={profileTagClass}>
                                            {strength}
                                        </span>
                                    ))}
                                </div>
                            </div>

                            <p className="mt-4 text-sm leading-relaxed text-slate-600">{profileAnalysis.summary}</p>
                        </article>

                        <article className="relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-lg lg:col-span-2">
                            <div className="mb-4 flex items-start justify-between">
                                <div>
                                    <h3 className="text-lg font-bold text-slate-900">Dimensoes do Perfil</h3>
                                    <p className="text-sm text-slate-500">Visualizacao das suas competencias e interesses.</p>
                                </div>
                                <button type="button" className="text-sm font-semibold text-blue-600 hover:underline">
                                    Ver detalhes completos
                                </button>
                            </div>

                            <ProfileRadar dimensions={dimensions} />

                            <div className="absolute -bottom-10 -right-10 h-32 w-32 rounded-full bg-blue-100 blur-2xl" />
                            <div className="absolute -left-10 -top-10 h-32 w-32 rounded-full bg-indigo-100 blur-2xl" />
                        </article>
                    </section>

                    <section className="mb-6 flex items-center justify-between">
                        <h2 className="text-2xl font-extrabold text-slate-900">Cursos Recomendados</h2>
                        <div className="flex gap-2">
                            <button type="button" className="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                                Filtrar
                            </button>
                            <button type="button" className="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                                Ordenar por Match
                            </button>
                        </div>
                    </section>

                    <section className="mb-12 grid grid-cols-1 gap-6 md:grid-cols-2">
                        {recommendations.map((recommendation) => {
                            const badge = scoreBadge(recommendation.compatibility_score);
                            const careerTitles = recommendation.career_paths.slice(0, 3);

                            return (
                                <article
                                    key={`${recommendation.course_name}-${recommendation.rank}`}
                                    className="group relative overflow-hidden rounded-2xl border border-slate-200 bg-white p-6 shadow-lg transition hover:shadow-xl"
                                >
                                    <div className={`absolute left-0 top-0 h-full w-1.5 ${badge.accent}`} />

                                    <div className="mb-4 pl-4">
                                        <div className="mb-2 flex items-start justify-between gap-3">
                                            <div>
                                                <span className={`inline-flex rounded px-2 py-1 text-xs font-semibold ${badge.chip}`}>{badge.text}</span>
                                                <h3 className="mt-2 text-xl font-bold text-slate-900">{recommendation.course_name}</h3>
                                                <p className="text-sm text-slate-500">
                                                    {recommendation.course.course_type} • {recommendation.course.duration_years_label}
                                                </p>
                                            </div>

                                            <div className="flex h-16 w-16 flex-col items-center justify-center rounded-lg border border-slate-200 bg-slate-50">
                                                <span className="text-2xl font-extrabold text-blue-600">
                                                    {recommendation.compatibility_score.toFixed(0)}%
                                                </span>
                                                <span className="text-[10px] font-bold uppercase tracking-wide text-slate-400">Match</span>
                                            </div>
                                        </div>

                                        <div className="space-y-4">
                                            <div>
                                                <h4 className="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                                    <Sparkles className="h-4 w-4 text-blue-500" />
                                                    Por que combinamos?
                                                </h4>
                                                <p className="text-sm leading-relaxed text-slate-600">{recommendation.justification}</p>
                                            </div>

                                            <div className="border-t border-slate-100 pt-4">
                                                <h4 className="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">
                                                    Principais Saidas Profissionais
                                                </h4>
                                                <div className="flex flex-wrap gap-2">
                                                    {careerTitles.map((careerPath, index) => (
                                                        <span
                                                            key={`${careerPath.title}-${index}`}
                                                            className="inline-flex rounded-full bg-blue-50 px-3 py-1 text-xs font-medium text-blue-700"
                                                        >
                                                            {careerPath.title}
                                                        </span>
                                                    ))}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div className="mt-6 flex gap-3 pl-4">
                                        <button
                                            type="button"
                                            className="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-blue-300 transition hover:bg-blue-700"
                                            onClick={() => router.visit(`/assessment/${assessment.id}/results/course/${recommendation.rank}`)}
                                        >
                                            Ver Detalhes do Curso
                                        </button>
                                        <button
                                            type="button"
                                            className="rounded-lg border border-slate-200 bg-white px-4 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50"
                                        >
                                            Salvar
                                        </button>
                                    </div>
                                </article>
                            );
                        })}

                        <article className="flex flex-col items-center justify-center rounded-2xl border border-dashed border-blue-200 bg-gradient-to-br from-blue-50 to-indigo-50 p-6 text-center">
                            <div className="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-white shadow-sm">
                                <Search className="h-7 w-7 text-blue-600" />
                            </div>
                            <h3 className="text-lg font-bold text-slate-900">Explore Mais Opcoes</h3>
                            <p className="mt-2 max-w-xs text-sm text-slate-600">
                                Temos outros cursos que tambem podem combinar com seu perfil.
                            </p>
                            <button
                                type="button"
                                className="mt-5 rounded-lg border border-slate-200 bg-white px-6 py-2 text-sm font-semibold text-blue-600 hover:bg-slate-50"
                            >
                                Ver Todos os Cursos
                            </button>
                        </article>
                    </section>

                    <section className="relative overflow-hidden rounded-2xl bg-blue-600 p-8 text-white">
                        <div className="relative z-10 flex flex-col items-center justify-between gap-6 md:flex-row">
                            <div className="text-center md:text-left">
                                <h2 className="text-2xl font-bold">Pronto para o proximo passo?</h2>
                                <p className="mt-2 max-w-lg text-blue-100">
                                    Agende uma conversa com um orientador vocacional para discutir seus resultados em detalhes.
                                </p>
                            </div>
                            <button
                                type="button"
                                className="whitespace-nowrap rounded-lg bg-white px-6 py-3 font-bold text-blue-700 transition hover:bg-blue-50"
                                onClick={() => router.visit(`/assessment/${assessment.id}/results/course/${topRecommendation?.rank ?? 1}`)}
                            >
                                Agendar Mentoria
                            </button>
                        </div>

                        <div className="absolute -right-10 -top-10 h-40 w-40 rounded-full bg-white/10" />
                        <div className="absolute -bottom-10 -left-10 h-40 w-40 rounded-full bg-white/10" />
                    </section>

                    <section className="mt-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                        <article className="rounded-xl border border-slate-200 bg-white p-4">
                            <h3 className="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <Compass className="h-4 w-4 text-blue-600" />
                                Proximos passos
                            </h3>
                            <p className="text-sm text-slate-600">
                                {additionalAdvice.immediate_next_steps || 'Defina as primeiras acoes praticas para esta semana.'}
                            </p>
                        </article>

                        <article className="rounded-xl border border-slate-200 bg-white p-4">
                            <h3 className="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <Medal className="h-4 w-4 text-blue-600" />
                                Visao de longo prazo
                            </h3>
                            <p className="text-sm text-slate-600">{additionalAdvice.long_term_vision || 'Mantenha consistencia no seu plano de evolucao.'}</p>
                        </article>

                        <article className="rounded-xl border border-slate-200 bg-white p-4">
                            <h3 className="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                <BookOpen className="h-4 w-4 text-blue-600" />
                                Comunidades sugeridas
                            </h3>
                            <ul className="space-y-1 text-sm text-slate-600">
                                {roadmap.communities_to_join.slice(0, 4).map((community, index) => (
                                    <li key={`${community}-${index}`} className="flex items-start gap-2">
                                        <span className="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500" />
                                        <span>{community}</span>
                                    </li>
                                ))}
                            </ul>
                        </article>
                    </section>
                </main>

                <footer className="mt-10 border-t border-slate-200 bg-slate-900 py-12 text-slate-400">
                    <div className="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 sm:px-6 md:grid-cols-4 lg:px-8">
                        <div>
                            <h4 className="mb-4 text-xl font-bold text-white">IMETRO</h4>
                            <p className="text-sm">
                                Plataforma de orientacao vocacional baseada em inteligencia artificial para estudantes.
                            </p>
                            <p className="mt-4 text-xs text-slate-500">Resultado da avaliacao #{assessment.id}</p>
                        </div>

                        <div>
                            <h5 className="mb-4 font-semibold text-white">Recursos</h5>
                            <ul className="space-y-2 text-sm">
                                <li>Testes Vocacionais</li>
                                <li>Analise de Perfil</li>
                                <li>Blog de Carreiras</li>
                            </ul>
                        </div>

                        <div>
                            <h5 className="mb-4 font-semibold text-white">Suporte</h5>
                            <ul className="space-y-2 text-sm">
                                <li>Central de Ajuda</li>
                                <li>Fale Conosco</li>
                                <li>Termos de Uso</li>
                            </ul>
                        </div>

                        <div>
                            <h5 className="mb-4 font-semibold text-white">Legal</h5>
                            <ul className="space-y-2 text-sm">
                                <li>Politica de Privacidade</li>
                                <li>LGPD</li>
                                <li>Cookies</li>
                            </ul>
                        </div>
                    </div>
                </footer>
            </div>
        </>
    );
}

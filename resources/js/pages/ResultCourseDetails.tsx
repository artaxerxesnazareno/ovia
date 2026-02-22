import { Head, router } from '@inertiajs/react';
import { ArrowLeft, BookOpen, Brain, Briefcase, Compass, GraduationCap, Sparkles, Target } from 'lucide-react';

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

interface ResultCourseDetailsProps {
    assessment: AssessmentData;
    profileAnalysis: ProfileAnalysis;
    selectedRecommendation: CourseRecommendation;
    recommendations: CourseRecommendation[];
    roadmap: Roadmap;
    additionalAdvice: AdditionalAdvice;
}

const timelineData = (roadmap: Roadmap) => [
    { key: 'short', label: 'Curto Prazo', items: roadmap.short_term },
    { key: 'medium', label: 'Medio Prazo', items: roadmap.medium_term },
    { key: 'long', label: 'Longo Prazo', items: roadmap.long_term },
] as const;

const scoreBadge = (score: number) => {
    if (score >= 90) return { text: 'Alta Compatibilidade', className: 'bg-emerald-50 text-emerald-700 border-emerald-200' };
    if (score >= 80) return { text: 'Boa Compatibilidade', className: 'bg-blue-50 text-blue-700 border-blue-200' };
    return { text: 'Media Compatibilidade', className: 'bg-amber-50 text-amber-700 border-amber-200' };
};

export default function ResultCourseDetails({
    assessment,
    profileAnalysis,
    selectedRecommendation,
    recommendations,
    roadmap,
    additionalAdvice,
}: ResultCourseDetailsProps) {
    const badge = scoreBadge(selectedRecommendation.compatibility_score);

    return (
        <>
            <Head title={`Detalhes: ${selectedRecommendation.course_name}`} />

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

                        <div className="flex items-center gap-3">
                            <button
                                type="button"
                                className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                                onClick={() => router.visit(`/assessment/${assessment.id}/results`)}
                            >
                                <ArrowLeft className="h-4 w-4" />
                                Voltar aos resultados
                            </button>
                        </div>
                    </div>
                </header>

                <main className="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
                    <section className="mb-8 text-center">
                        <span className="inline-flex items-center gap-1 rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-blue-700">
                            <Sparkles className="h-3.5 w-3.5" />
                            Detalhes do curso recomendado
                        </span>
                        <h1 className="mt-4 text-3xl font-extrabold text-slate-900 md:text-4xl">{selectedRecommendation.course_name}</h1>
                        <p className="mx-auto mt-3 max-w-3xl text-slate-600">
                            Entenda por que este curso combina com seu perfil e como planejar seus proximos passos.
                        </p>
                    </section>

                    <section className="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <article className="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg lg:col-span-2">
                            <div className="mb-4 flex flex-wrap items-start justify-between gap-3">
                                <div>
                                    <span className={`inline-flex items-center rounded border px-2 py-1 text-xs font-semibold ${badge.className}`}>
                                        {badge.text}
                                    </span>
                                    <h2 className="mt-2 text-2xl font-extrabold text-slate-900">{selectedRecommendation.course_name}</h2>
                                    <p className="text-sm text-slate-500">
                                        {selectedRecommendation.course.course_type} • {selectedRecommendation.course.duration_years_label}
                                    </p>
                                </div>

                                <div className="flex h-16 w-16 flex-col items-center justify-center rounded-lg border border-slate-200 bg-slate-50">
                                    <span className="text-2xl font-extrabold text-blue-600">
                                        {selectedRecommendation.compatibility_score.toFixed(0)}%
                                    </span>
                                    <span className="text-[10px] font-bold uppercase tracking-wide text-slate-400">Match</span>
                                </div>
                            </div>

                            <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div className="rounded-xl bg-slate-50 p-4">
                                    <h3 className="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <BookOpen className="h-4 w-4 text-blue-600" />
                                        Sobre o curso
                                    </h3>
                                    <p className="text-sm leading-relaxed text-slate-600">
                                        {selectedRecommendation.course.description || 'Descricao nao informada.'}
                                    </p>
                                </div>

                                <div className="rounded-xl bg-slate-50 p-4">
                                    <h3 className="mb-2 flex items-center gap-2 text-sm font-semibold text-slate-700">
                                        <GraduationCap className="h-4 w-4 text-blue-600" />
                                        Informacoes academicas
                                    </h3>
                                    <ul className="space-y-1 text-sm text-slate-600">
                                        <li>
                                            <strong>Turnos:</strong>{' '}
                                            {selectedRecommendation.course.shifts.length > 0
                                                ? selectedRecommendation.course.shifts.join(', ')
                                                : 'Nao informado'}
                                        </li>
                                        <li>
                                            <strong>Vagas/ano:</strong>{' '}
                                            {selectedRecommendation.course.vacancies_per_year ?? 'Nao informado'}
                                        </li>
                                        <li>
                                            <strong>Coordenador:</strong>{' '}
                                            {selectedRecommendation.course.coordinator_name || 'Nao informado'}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </article>

                        <article className="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
                            <div className="mb-3 flex items-center gap-2">
                                <Brain className="h-5 w-5 text-blue-600" />
                                <h3 className="text-base font-bold text-slate-900">Perfil em destaque</h3>
                            </div>
                            <h4 className="text-sm font-semibold text-slate-700">{profileAnalysis.personality_type || 'Perfil em definicao'}</h4>
                            <p className="mt-2 text-sm leading-relaxed text-slate-600">{profileAnalysis.summary}</p>

                            <div className="mt-4">
                                <h5 className="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">Pontos fortes para este curso</h5>
                                <div className="flex flex-wrap gap-2">
                                    {selectedRecommendation.student_strengths_for_course.map((strength, index) => (
                                        <span
                                            key={`${strength}-${index}`}
                                            className="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-medium text-emerald-700"
                                        >
                                            {strength}
                                        </span>
                                    ))}
                                </div>
                            </div>
                        </article>
                    </section>

                    <section className="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
                        <article className="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg lg:col-span-2">
                            <h3 className="mb-3 flex items-center gap-2 text-lg font-bold text-slate-900">
                                <Target className="h-5 w-5 text-blue-600" />
                                Por que este curso combina com voce
                            </h3>
                            <p className="text-sm leading-relaxed text-slate-600">{selectedRecommendation.justification}</p>

                            <div className="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                                <div className="rounded-xl bg-slate-50 p-4">
                                    <h4 className="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">Desafios potenciais</h4>
                                    <ul className="space-y-2 text-sm text-slate-600">
                                        {selectedRecommendation.potential_challenges.map((challenge, index) => (
                                            <li key={`${challenge}-${index}`} className="flex items-start gap-2">
                                                <span className="mt-1 h-1.5 w-1.5 rounded-full bg-amber-500" />
                                                <span>{challenge}</span>
                                            </li>
                                        ))}
                                    </ul>
                                </div>

                                <div className="rounded-xl bg-slate-50 p-4">
                                    <h4 className="mb-2 text-xs font-bold uppercase tracking-wide text-slate-500">Requisitos de admissao</h4>
                                    <p className="text-sm text-slate-600">
                                        {selectedRecommendation.course.admission_requirements || 'Nao informado.'}
                                    </p>
                                </div>
                            </div>
                        </article>

                        <article className="rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
                            <h3 className="mb-3 flex items-center gap-2 text-base font-bold text-slate-900">
                                <Compass className="h-5 w-5 text-blue-600" />
                                Outros cursos recomendados
                            </h3>

                            <div className="space-y-3">
                                {recommendations.map((recommendation) => (
                                    <button
                                        key={`${recommendation.course_name}-${recommendation.rank}`}
                                        type="button"
                                        className={`w-full rounded-lg border p-3 text-left transition ${
                                            recommendation.rank === selectedRecommendation.rank
                                                ? 'border-blue-300 bg-blue-50'
                                                : 'border-slate-200 bg-white hover:bg-slate-50'
                                        }`}
                                        onClick={() => router.visit(`/assessment/${assessment.id}/results/course/${recommendation.rank}`)}
                                    >
                                        <p className="text-sm font-semibold text-slate-800">{recommendation.course_name}</p>
                                        <p className="text-xs text-slate-500">Match {recommendation.compatibility_score.toFixed(0)}%</p>
                                    </button>
                                ))}
                            </div>
                        </article>
                    </section>

                    <section className="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
                        <h3 className="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900">
                            <Briefcase className="h-5 w-5 text-blue-600" />
                            Saidas profissionais
                        </h3>

                        <div className="grid grid-cols-1 gap-4 md:grid-cols-2">
                            {selectedRecommendation.career_paths.map((careerPath, index) => (
                                <article key={`${careerPath.title}-${index}`} className="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                    <h4 className="text-base font-bold text-slate-800">{careerPath.title}</h4>
                                    <p className="mt-1 text-sm text-slate-600">{careerPath.description}</p>
                                    <div className="mt-3 space-y-1 text-xs text-slate-500">
                                        <p>
                                            <strong>Faixa salarial:</strong> {careerPath.average_salary_range || 'Nao informado'}
                                        </p>
                                        <p>
                                            <strong>Demanda:</strong> {careerPath.market_demand || 'Nao informado'}
                                        </p>
                                        <p>
                                            <strong>Crescimento:</strong> {careerPath.growth_potential || 'Nao informado'}
                                        </p>
                                    </div>
                                </article>
                            ))}
                        </div>
                    </section>

                    <section className="mb-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-lg">
                        <h3 className="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900">
                            <GraduationCap className="h-5 w-5 text-blue-600" />
                            Grade curricular e roadmap
                        </h3>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-3">
                            <article className="rounded-xl border border-slate-200 bg-slate-50 p-4 lg:col-span-1">
                                <h4 className="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">Topicos da grade</h4>
                                <ul className="space-y-1.5 text-sm text-slate-600">
                                    {selectedRecommendation.course.curriculum_topics.slice(0, 14).map((topic, index) => (
                                        <li key={`${topic}-${index}`} className="flex items-start gap-2">
                                            <span className="mt-1 h-1.5 w-1.5 rounded-full bg-blue-500" />
                                            <span>{topic}</span>
                                        </li>
                                    ))}
                                </ul>
                            </article>

                            <article className="lg:col-span-2">
                                <div className="grid grid-cols-1 gap-4 md:grid-cols-3">
                                    {timelineData(roadmap).map((section) => (
                                        <div key={section.key} className="rounded-xl border border-slate-200 bg-slate-50 p-4">
                                            <h4 className="mb-3 text-xs font-bold uppercase tracking-wide text-slate-500">{section.label}</h4>
                                            <div className="space-y-3">
                                                {section.items.slice(0, 2).map((goal, index) => (
                                                    <div key={`${goal.goal}-${index}`} className="rounded-lg bg-white p-3">
                                                        <p className="text-sm font-semibold text-slate-800">{goal.goal}</p>
                                                        <p className="mt-1 text-xs text-slate-500">{goal.timeframe}</p>
                                                    </div>
                                                ))}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </article>
                        </div>
                    </section>

                    <section className="rounded-2xl bg-blue-600 p-6 text-white">
                        <h3 className="text-xl font-bold">Mensagem final para voce</h3>
                        <p className="mt-2 text-blue-100">{additionalAdvice.words_of_encouragement || 'Continue evoluindo com foco e constancia.'}</p>
                        <p className="mt-3 text-sm text-blue-100">{additionalAdvice.immediate_next_steps}</p>
                    </section>
                </main>

                <footer className="mt-10 border-t border-slate-200 bg-slate-900 py-12 text-slate-400">
                    <div className="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 sm:px-6 md:grid-cols-4 lg:px-8">
                        <div>
                            <h4 className="mb-4 text-xl font-bold text-white">IMETRO</h4>
                            <p className="text-sm">
                                Plataforma de orientacao vocacional baseada em inteligencia artificial para estudantes.
                            </p>
                            <p className="mt-4 text-xs text-slate-500">Detalhes da avaliacao #{assessment.id}</p>
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

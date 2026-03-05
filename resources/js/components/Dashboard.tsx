import { router } from '@inertiajs/react';
import { ArrowRight, Bell, Brain, Calculator, Check, ChevronRight, Circle, ClipboardCheck, Sparkles, Star, UserSearch } from 'lucide-react';

import { Button } from '@/components/ui/button';

type AssessmentStatus = 'completed' | 'pending' | 'processing' | 'failed';

interface DashboardAssessment {
    id: number;
    title: string;
    completedAt?: string | null;
    status: AssessmentStatus;
    resultLabel?: string | null;
    resultHint?: string | null;
    actionLabel?: string | null;
    actionUrl?: string | null;
}

interface DashboardRecommendation {
    id: number;
    title: string;
    description: string;
    compatibility: number;
    actionUrl?: string | null;
}

interface DashboardTask {
    id: string;
    label: string;
    done: boolean;
}

export interface DashboardData {
    roadmapProgress?: number;
    currentStage?: string;
    remainingActivities?: number;
    assessments?: DashboardAssessment[];
    recommendations?: DashboardRecommendation[];
    tasks?: DashboardTask[];
}

interface DashboardProps {
    onStartAssessment: () => void;
    data?: DashboardData;
}

const normalizeProgress = (value: number) => Math.max(0, Math.min(100, Math.round(value)));

const formatCompletedAt = (value?: string | null): string => {
    if (!value) {
        return '-';
    }

    const date = new Date(value);
    if (Number.isNaN(date.getTime())) {
        return '-';
    }

    return new Intl.DateTimeFormat('pt-BR', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
    }).format(date);
};

const assessmentPresentation = (status: AssessmentStatus) => {
    if (status === 'completed') {
        return {
            icon: Brain,
            wrapperClass: 'bg-blue-50 text-blue-600',
            resultClass: 'text-emerald-600',
        };
    }

    if (status === 'processing') {
        return {
            icon: Calculator,
            wrapperClass: 'bg-indigo-50 text-indigo-600',
            resultClass: 'text-indigo-600',
        };
    }

    if (status === 'failed') {
        return {
            icon: Circle,
            wrapperClass: 'bg-rose-50 text-rose-600',
            resultClass: 'text-rose-600',
        };
    }

    return {
        icon: UserSearch,
        wrapperClass: 'bg-slate-100 text-slate-500',
        resultClass: 'text-slate-600',
    };
};

const recommendationChipClass = (compatibility: number) => {
    if (compatibility >= 90) return 'bg-emerald-50 text-emerald-700';
    if (compatibility >= 80) return 'bg-blue-50 text-blue-700';
    return 'bg-indigo-50 text-indigo-700';
};

const subtitleByStatus = (assessment: DashboardAssessment): string => {
    if (assessment.status === 'completed') {
        return `Concluido em ${formatCompletedAt(assessment.completedAt)}`;
    }

    if (assessment.status === 'processing') {
        return 'Em processamento';
    }

    if (assessment.status === 'failed') {
        return 'Falhou no processamento';
    }

    return 'Pendente';
};

const Dashboard = ({ onStartAssessment, data }: DashboardProps) => {
    const assessments = data?.assessments ?? [];
    const recommendations = data?.recommendations ?? [];
    const tasks = data?.tasks ?? [];

    const hasAssessments = assessments.length > 0;
    const hasRecommendations = recommendations.length > 0;
    const hasTasks = tasks.length > 0;

    const pendingActivities = assessments.filter((assessment) => assessment.status === 'pending' || assessment.status === 'processing').length;

    const roadmapProgress = normalizeProgress(data?.roadmapProgress ?? 0);
    const currentStage = data?.currentStage ?? (hasAssessments ? 'Em andamento' : 'Nao iniciado');
    const remainingActivities = typeof data?.remainingActivities === 'number' ? Math.max(0, data.remainingActivities) : pendingActivities;

    const progressDegrees = roadmapProgress * 3.6;

    return (
        <div className="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            <div className="mb-10">
                <h1 className="mb-2 text-3xl font-extrabold text-slate-900">Seu Dashboard de Atividade</h1>
                <p className="text-slate-600">Acompanhe seu progresso na jornada de orientacao vocacional IA.</p>
            </div>

            <div className="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div className="space-y-8 lg:col-span-2">
                    <section className="flex flex-col items-center gap-8 rounded-2xl border border-slate-100 bg-white p-6 shadow-lg md:flex-row">
                        <div
                            className="relative h-32 w-32 rounded-full"
                            style={{
                                background: `conic-gradient(#3b82f6 ${progressDegrees}deg, #e2e8f0 0deg)`,
                            }}
                            aria-label={`Progresso do roadmap em ${roadmapProgress}%`}
                        >
                            <div className="absolute inset-2 rounded-full bg-white" />
                            <div className="relative z-10 flex h-full items-center justify-center">
                                <div className="text-center">
                                    <span className="text-2xl font-bold text-slate-900">{roadmapProgress}%</span>
                                    <p className="text-[10px] uppercase text-slate-500">Progresso</p>
                                </div>
                            </div>
                        </div>

                        <div className="flex-1 text-center md:text-left">
                            <h3 className="mb-2 text-xl font-bold text-slate-900">Progresso do Roadmap</h3>
                            <p className="mb-4 text-sm text-slate-500">
                                {hasAssessments
                                    ? 'Voce esta no caminho certo! Complete as proximas atividades para desbloquear sua analise de carreira final.'
                                    : 'Ainda nao encontramos atividades registradas. Inicie sua primeira avaliacao para montar seu roadmap.'}
                            </p>
                            <div className="flex flex-wrap justify-center gap-4 md:justify-start">
                                <div className="flex items-center gap-2">
                                    <span className="h-3 w-3 rounded-full bg-blue-500" />
                                    <span className="text-xs font-medium">Etapa Atual: {currentStage}</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <span className="h-3 w-3 rounded-full bg-slate-200" />
                                    <span className="text-xs font-medium">Restam: {remainingActivities} Atividades</span>
                                </div>
                            </div>
                        </div>

                        <Button
                            className="w-full rounded-xl bg-blue-500 px-6 py-2.5 font-semibold text-white shadow-lg shadow-blue-500/20 hover:bg-blue-600 md:w-auto"
                            onClick={onStartAssessment}
                        >
                            {hasAssessments ? 'Continuar Roadmap' : 'Iniciar Avaliacao'}
                        </Button>
                    </section>

                    <section className="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-lg">
                        <div className="flex items-center justify-between border-b border-slate-50 p-6">
                            <h2 className="flex items-center gap-2 text-lg font-bold text-slate-900">
                                <ClipboardCheck className="h-5 w-5 text-blue-500" />
                                Minhas Avaliacoes
                            </h2>
                            <button type="button" className="text-sm font-medium text-blue-500 hover:underline">
                                Ver todas
                            </button>
                        </div>

                        {hasAssessments ? (
                            <div className="divide-y divide-slate-50">
                                {assessments.map((assessment) => {
                                    const presentation = assessmentPresentation(assessment.status);
                                    const Icon = presentation.icon;
                                    const actionLabel = assessment.actionLabel ?? 'Continuar';

                                    return (
                                        <div
                                            key={assessment.id}
                                            className={`flex items-center justify-between p-6 transition-colors ${
                                                assessment.status === 'pending' ? 'opacity-80' : 'hover:bg-slate-50'
                                            }`}
                                        >
                                            <div className="flex items-center gap-4">
                                                <div className={`flex h-12 w-12 items-center justify-center rounded-xl ${presentation.wrapperClass}`}>
                                                    <Icon className="h-5 w-5" />
                                                </div>
                                                <div>
                                                    <h4 className="font-bold text-slate-900">{assessment.title}</h4>
                                                    <p className="text-xs text-slate-500">{subtitleByStatus(assessment)}</p>
                                                </div>
                                            </div>

                                            {assessment.status === 'completed' ? (
                                                <div className="flex items-center gap-6">
                                                    <div className="text-right">
                                                        <span className={`text-sm font-bold ${presentation.resultClass}`}>
                                                            {assessment.resultLabel}
                                                        </span>
                                                        <p className="text-[10px] uppercase text-slate-400">{assessment.resultHint}</p>
                                                    </div>
                                                    <button
                                                        type="button"
                                                        className="rounded p-2 text-slate-400 transition-colors hover:text-blue-500"
                                                        onClick={() =>
                                                            assessment.actionUrl ? router.visit(assessment.actionUrl) : onStartAssessment()
                                                        }
                                                    >
                                                        <ChevronRight className="h-4 w-4" />
                                                    </button>
                                                </div>
                                            ) : (
                                                <Button
                                                    type="button"
                                                    variant="outline"
                                                    className="rounded-lg border-blue-500 px-4 py-1.5 text-xs font-bold text-blue-500 hover:bg-blue-500 hover:text-white"
                                                    onClick={() => (assessment.actionUrl ? router.visit(assessment.actionUrl) : onStartAssessment())}
                                                >
                                                    {actionLabel}
                                                </Button>
                                            )}
                                        </div>
                                    );
                                })}
                            </div>
                        ) : (
                            <div className="p-10 text-center">
                                <p className="text-sm text-slate-500">Nenhuma avaliacao encontrada ate o momento.</p>
                                <Button className="mt-4 bg-blue-500 text-white hover:bg-blue-600" onClick={onStartAssessment}>
                                    Iniciar primeira avaliacao
                                </Button>
                            </div>
                        )}
                    </section>
                </div>

                <div className="space-y-8 lg:col-span-1">
                    <section className="rounded-2xl border border-slate-100 bg-white p-6 shadow-lg">
                        <h3 className="mb-6 flex items-center gap-2 text-lg font-bold text-slate-900">
                            <Star className="h-5 w-5 text-yellow-500" />
                            Recomendacoes Recentes
                        </h3>

                        {hasRecommendations ? (
                            <div className="space-y-4">
                                {recommendations.map((recommendation) => (
                                    <button
                                        type="button"
                                        key={recommendation.id}
                                        className="group block w-full cursor-pointer rounded-xl border border-slate-100 p-4 text-left transition-colors hover:border-blue-500/50"
                                        onClick={() => (recommendation.actionUrl ? router.visit(recommendation.actionUrl) : onStartAssessment())}
                                    >
                                        <div className="mb-2 flex items-start justify-between">
                                            <h4 className="font-bold text-slate-900 transition-colors group-hover:text-blue-500">
                                                {recommendation.title}
                                            </h4>
                                            <span
                                                className={`rounded px-2 py-0.5 text-xs font-bold ${recommendationChipClass(
                                                    recommendation.compatibility,
                                                )}`}
                                            >
                                                {recommendation.compatibility}%
                                            </span>
                                        </div>
                                        <p className="mb-3 text-xs text-slate-500">{recommendation.description}</p>
                                        <span className="flex items-center gap-1 text-xs font-medium text-blue-500">
                                            Ver detalhes <ArrowRight className="h-3 w-3" />
                                        </span>
                                    </button>
                                ))}
                            </div>
                        ) : (
                            <div className="rounded-xl border border-dashed border-slate-300 p-6 text-center">
                                <p className="text-sm text-slate-500">Ainda nao ha recomendacoes. Complete uma avaliacao para gerar sugestoes.</p>
                            </div>
                        )}
                    </section>

                    <section className="relative overflow-hidden rounded-2xl border border-slate-100 bg-white p-6 shadow-lg">
                        <div className="relative z-10">
                            <h3 className="mb-4 flex items-center gap-2 text-lg font-bold text-slate-900">
                                <Bell className="h-5 w-5 text-blue-500" />
                                Proximos Passos
                            </h3>

                            {hasTasks ? (
                                <div className="space-y-4">
                                    {tasks.map((task) => (
                                        <label
                                            key={task.id}
                                            className="group flex cursor-pointer items-center gap-3 rounded-lg border border-slate-100 bg-slate-50 p-3 transition hover:border-blue-200"
                                        >
                                            <span className="flex h-4 w-4 items-center justify-center">
                                                {task.done ? (
                                                    <Check className="h-4 w-4 text-blue-500" />
                                                ) : (
                                                    <Circle className="h-4 w-4 text-slate-400" />
                                                )}
                                            </span>
                                            <span className="text-sm font-medium text-slate-700 transition-colors group-hover:text-blue-600">
                                                {task.label}
                                            </span>
                                        </label>
                                    ))}
                                </div>
                            ) : (
                                <div className="rounded-lg border border-dashed border-slate-300 p-4">
                                    <p className="text-sm text-slate-600">Sem tarefas definidas por enquanto.</p>
                                    <p className="mt-1 text-xs text-slate-500">
                                        Conclua uma avaliacao para montarmos os proximos passos automaticamente.
                                    </p>
                                </div>
                            )}
                        </div>

                        <div className="absolute -right-10 -top-10 h-32 w-32 rounded-full bg-blue-100 blur-2xl" />
                        <div className="absolute -bottom-10 -left-10 h-24 w-24 rounded-full bg-indigo-100 blur-xl" />
                        <Sparkles className="pointer-events-none absolute right-4 top-4 h-4 w-4 text-blue-400/80" />
                    </section>
                </div>
            </div>
        </div>
    );
};

export default Dashboard;

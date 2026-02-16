import { Head, router } from '@inertiajs/react';
import { useEffect, useMemo, useState } from 'react';
import { AlertCircle, Brain, CheckCircle, Loader2, Sparkles } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';

type AssessmentStatus = 'pending' | 'processing' | 'completed' | 'failed';

interface ProcessingProps {
    assessment: {
        id: number;
        status: AssessmentStatus;
        completed_at: string | null;
        total_questions: number;
    };
    ux: {
        poll_interval_ms: number;
        timeout_seconds: number;
    };
}

interface StatusResponse {
    status: AssessmentStatus;
    completed_at: string | null;
    redirect_url?: string;
    message?: string;
}

interface ProcessingStep {
    id: number;
    title: string;
    description: string;
    duration: number;
}

const encouragingMessages = [
    'Voce esta a poucos instantes do seu mapa vocacional personalizado.',
    'Estamos cruzando suas respostas com dezenas de possibilidades de carreira.',
    'Cada resposta ajuda a IA a montar recomendacoes mais precisas para voce.',
    'Seu resultado ja esta quase pronto. Obrigado por aguardar.',
];

const getProcessingSteps = (totalQuestions: number): ProcessingStep[] => [
    {
        id: 1,
        title: 'Analisando suas respostas',
        description: `Processando ${totalQuestions} questoes em 4 categorias`,
        duration: 2800,
    },
    {
        id: 2,
        title: 'Comparando com cursos do IMETRO',
        description: 'Mapeando afinidade com as trilhas disponiveis',
        duration: 3600,
    },
    {
        id: 3,
        title: 'Gerando recomendacoes personalizadas',
        description: 'Calculando combinacoes de perfil, interesse e potencial',
        duration: 4200,
    },
    {
        id: 4,
        title: 'Finalizando seu plano inicial',
        description: 'Organizando os proximos passos para sua jornada',
        duration: 2800,
    },
];

export default function Processing({ assessment, ux }: ProcessingProps) {
    const processingSteps = useMemo(
        () => getProcessingSteps(assessment.total_questions),
        [assessment.total_questions],
    );

    const [currentStep, setCurrentStep] = useState(0);
    const [progress, setProgress] = useState(10);
    const [elapsedTime, setElapsedTime] = useState(0);
    const [messageIndex, setMessageIndex] = useState(0);
    const [failedMessage, setFailedMessage] = useState<string | null>(
        assessment.status === 'failed'
            ? 'Nao foi possivel concluir o processamento agora. Tente novamente.'
            : null,
    );
    const [isDelayed, setIsDelayed] = useState(false);

    useEffect(() => {
        if (assessment.status === 'completed') {
            router.visit('/app-dashboard');
        }
    }, [assessment.status]);

    useEffect(() => {
        if (failedMessage) {
            return;
        }

        const elapsedInterval = window.setInterval(() => {
            setElapsedTime((prev) => prev + 1);
        }, 1000);

        return () => window.clearInterval(elapsedInterval);
    }, [failedMessage]);

    useEffect(() => {
        if (failedMessage || elapsedTime < ux.timeout_seconds) {
            return;
        }

        setIsDelayed(true);
    }, [elapsedTime, failedMessage, ux.timeout_seconds]);

    useEffect(() => {
        if (failedMessage) {
            return;
        }

        if (currentStep >= processingSteps.length - 1) {
            return;
        }

        const stepTimer = window.setTimeout(() => {
            setCurrentStep((prev) => Math.min(prev + 1, processingSteps.length - 1));
        }, processingSteps[currentStep].duration);

        return () => window.clearTimeout(stepTimer);
    }, [currentStep, failedMessage, processingSteps]);

    useEffect(() => {
        if (failedMessage) {
            return;
        }

        const progressInterval = window.setInterval(() => {
            setProgress((prev) => Math.min(prev + (isDelayed ? 0.5 : 1.8), 95));
        }, 1000);

        return () => window.clearInterval(progressInterval);
    }, [failedMessage, isDelayed]);

    useEffect(() => {
        const stepFloor = Math.min(90, ((currentStep + 1) / processingSteps.length) * 100 - 5);
        setProgress((prev) => Math.max(prev, stepFloor));
    }, [currentStep, processingSteps.length]);

    useEffect(() => {
        if (failedMessage) {
            return;
        }

        const messageInterval = window.setInterval(() => {
            setMessageIndex((prev) => (prev + 1) % encouragingMessages.length);
        }, 2600);

        return () => window.clearInterval(messageInterval);
    }, [failedMessage]);

    useEffect(() => {
        if (failedMessage) {
            return;
        }

        let isActive = true;

        const checkStatus = async () => {
            try {
                const response = await fetch(`/assessment/${assessment.id}/status`, {
                    method: 'GET',
                    headers: {
                        Accept: 'application/json',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    return;
                }

                const data = (await response.json()) as StatusResponse;
                if (!isActive) {
                    return;
                }

                if (data.status === 'completed') {
                    router.visit(data.redirect_url || '/app-dashboard');
                    return;
                }

                if (data.status === 'failed') {
                    setFailedMessage(
                        data.message || 'Nao foi possivel concluir o processamento agora. Tente novamente.',
                    );
                }
            } catch {
                // Mantemos o polling em falhas transientes de rede.
            }
        };

        void checkStatus();
        const statusInterval = window.setInterval(() => {
            void checkStatus();
        }, ux.poll_interval_ms);

        return () => {
            isActive = false;
            window.clearInterval(statusInterval);
        };
    }, [assessment.id, failedMessage, ux.poll_interval_ms]);

    const formatTime = (seconds: number) => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    const handleRetry = () => {
        router.post('/assessment/create');
    };

    const handleBackToStart = () => {
        router.visit('/assessment/start');
    };

    const handleGoDashboard = () => {
        router.visit('/app-dashboard');
    };

    return (
        <>
            <Head title="Processando Avaliacao" />

            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
                <div className="mx-auto max-w-2xl px-4 py-10">
                    <div className="mb-8 text-center">
                        <button
                            type="button"
                            className="text-3xl font-extrabold tracking-tight text-gray-900"
                            onClick={() => router.visit('/')}
                        >
                            OVIA
                        </button>
                        <p className="mt-2 text-sm text-gray-600">Analise vocacional em andamento</p>
                    </div>

                    {failedMessage ? (
                        <Card className="border-red-200 bg-red-50">
                            <CardContent className="p-8">
                                <div className="text-center">
                                    <AlertCircle className="mx-auto mb-3 h-10 w-10 text-red-600" />
                                    <h1 className="mb-2 text-2xl font-bold text-red-900">
                                        Nao conseguimos concluir agora
                                    </h1>
                                    <p className="mb-6 text-sm text-red-800">{failedMessage}</p>
                                    <div className="flex flex-col gap-3 sm:flex-row sm:justify-center">
                                        <Button onClick={handleRetry}>Tentar novamente</Button>
                                        <Button variant="outline" onClick={handleBackToStart}>
                                            Voltar ao inicio
                                        </Button>
                                    </div>
                                </div>
                            </CardContent>
                        </Card>
                    ) : (
                        <>
                            <div className="mb-8 text-center">
                                <div className="relative mx-auto mb-6 flex h-28 w-28 items-center justify-center">
                                    <span className="absolute inline-flex h-full w-full animate-ping rounded-full bg-blue-300/70" />
                                    <span className="absolute inline-flex h-20 w-20 animate-pulse rounded-full bg-purple-400/40" />
                                    <Loader2 className="absolute -right-1 -top-1 h-6 w-6 animate-spin text-blue-600" />
                                    <div className="relative flex h-16 w-16 items-center justify-center rounded-full bg-gradient-to-br from-blue-500 to-purple-600 shadow-lg">
                                        <Brain className="h-8 w-8 text-white" />
                                    </div>
                                </div>

                                <h1 className="mb-2 text-3xl font-bold text-gray-900">
                                    Processando sua avaliacao
                                </h1>
                                <p className="text-gray-600">
                                    Nossa IA esta cruzando suas respostas para montar seu perfil.
                                </p>
                            </div>

                            <Card className="mb-6">
                                <CardContent className="pt-6">
                                    <div className="space-y-3">
                                        <Progress value={progress} className="h-3" />
                                        <div className="flex justify-between text-sm text-gray-600">
                                            <span>Progresso estimado: {Math.round(progress)}%</span>
                                            <span>Tempo: {formatTime(elapsedTime)}</span>
                                        </div>
                                    </div>
                                </CardContent>
                            </Card>

                            <div className="mb-6 space-y-4">
                                {processingSteps.map((step, index) => {
                                    const isCompleted = index < currentStep;
                                    const isCurrent = index === currentStep;
                                    const isPending = index > currentStep;

                                    return (
                                        <Card
                                            key={step.id}
                                            className={`transition-all duration-300 ${
                                                isCurrent
                                                    ? 'scale-[1.02] border-blue-500 shadow-md'
                                                    : isCompleted
                                                      ? 'border-green-300 bg-green-50'
                                                      : isPending
                                                        ? 'opacity-60'
                                                        : ''
                                            }`}
                                        >
                                            <CardContent className="flex items-start gap-4 p-4">
                                                <div className="mt-0.5 flex-shrink-0">
                                                    {isCompleted ? (
                                                        <CheckCircle className="h-6 w-6 text-green-600" />
                                                    ) : isCurrent ? (
                                                        <Loader2 className="h-6 w-6 animate-spin text-blue-600" />
                                                    ) : (
                                                        <div className="h-6 w-6 rounded-full border-2 border-gray-300" />
                                                    )}
                                                </div>
                                                <div className="flex-1">
                                                    <h3 className="mb-1 font-semibold text-gray-900">{step.title}</h3>
                                                    <p className="text-sm text-gray-600">{step.description}</p>
                                                </div>
                                            </CardContent>
                                        </Card>
                                    );
                                })}
                            </div>

                            <Card className="mb-6 border-none bg-gradient-to-r from-blue-50 to-purple-50">
                                <CardContent className="p-6 text-center">
                                    <Sparkles className="mx-auto mb-3 h-8 w-8 text-purple-600" />
                                    <p className="text-sm text-gray-700">{encouragingMessages[messageIndex]}</p>
                                </CardContent>
                            </Card>

                            {isDelayed && (
                                <Card className="border-amber-300 bg-amber-50">
                                    <CardContent className="p-5">
                                        <div className="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <p className="font-semibold text-amber-900">
                                                    Estamos levando um pouco mais que o esperado.
                                                </p>
                                                <p className="text-sm text-amber-800">
                                                    Continuaremos verificando automaticamente sem perder seu progresso.
                                                </p>
                                            </div>
                                            <Button variant="outline" onClick={handleGoDashboard}>
                                                Ir para o dashboard
                                            </Button>
                                        </div>
                                    </CardContent>
                                </Card>
                            )}

                            <p className="mt-6 text-center text-xs text-gray-500">
                                Este processo pode levar de 15 a 30 segundos. Nao feche esta pagina.
                            </p>
                        </>
                    )}
                </div>
            </div>
        </>
    );
}

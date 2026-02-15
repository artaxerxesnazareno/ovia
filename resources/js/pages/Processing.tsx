import { Head, router, usePage } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import { Loader2, Brain, Sparkles, CheckCircle } from 'lucide-react';
import { Card, CardContent } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import Header from '@/components/Header';
import { SharedData } from '@/types';

interface ProcessingProps {
    assessment: {
        id: number;
        status: 'pending' | 'processing' | 'completed' | 'failed';
        completed_at: string;
    };
}

const processingSteps = [
    {
        id: 1,
        title: 'Analisando suas respostas',
        description: 'Processando {totalQuestions} questões em 4 categorias',
        duration: 3000,
    },
    {
        id: 2,
        title: 'Comparando com cursos do IMETRO',
        description: 'Avaliando compatibilidade com 10 cursos disponíveis',
        duration: 4000,
    },
    {
        id: 3,
        title: 'Gerando recomendações personalizadas',
        description: 'Inteligência artificial criando análise profunda do seu perfil',
        duration: 5000,
    },
    {
        id: 4,
        title: 'Preparando roadmap de desenvolvimento',
        description: 'Criando plano personalizado para sua jornada',
        duration: 3000,
    },
];

export default function Processing({ assessment }: ProcessingProps) {
    const { auth } = usePage<SharedData>().props;
    const [currentStep, setCurrentStep] = useState(0);
    const [progress, setProgress] = useState(0);
    const [elapsedTime, setElapsedTime] = useState(0);

    const handleHeaderNavigate = (view: string) => {
        if (view === 'assessment') {
            router.visit('/assessment/start');
            return;
        }

        if (view === 'dashboard') {
            router.visit('/app-dashboard');
            return;
        }

        router.visit('/');
    };

    // Polling para verificar status
    useEffect(() => {
        const checkStatus = async () => {
            try {
                const response = await fetch(`/assessment/${assessment.id}/status`);
                const data = await response.json();

                if (data.status === 'completed' && data.redirect_url) {
                    router.visit(data.redirect_url);
                } else if (data.status === 'failed') {
                    router.visit(`/assessment/${assessment.id}/error`);
                }
            } catch (error) {
                console.error('Error checking status:', error);
            }
        };

        // Verificar a cada 2 segundos
        const statusInterval = setInterval(checkStatus, 2000);

        return () => clearInterval(statusInterval);
    }, [assessment.id]);

    // Simular progressão dos steps
    useEffect(() => {
        if (currentStep >= processingSteps.length) return;

        const step = processingSteps[currentStep];
        const stepProgress = (currentStep / processingSteps.length) * 100;

        const timer = setTimeout(() => {
            setCurrentStep((prev) => prev + 1);
            setProgress(stepProgress + 100 / processingSteps.length);
        }, step.duration);

        return () => clearTimeout(timer);
    }, [currentStep]);

    // Contador de tempo
    useEffect(() => {
        const timer = setInterval(() => {
            setElapsedTime((prev) => prev + 1);
        }, 1000);

        return () => clearInterval(timer);
    }, []);

    const formatTime = (seconds: number) => {
        const mins = Math.floor(seconds / 60);
        const secs = seconds % 60;
        return `${mins}:${secs.toString().padStart(2, '0')}`;
    };

    return (
        <>
            <Head title="Processando Avaliação" />

            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
                <Header currentView="assessment" onNavigate={handleHeaderNavigate} userProgress={0} user={auth.user} />
                <div className="container mx-auto max-w-2xl px-4 py-12">
                    {/* Animação Principal */}
                    <div className="text-center mb-8">
                        <div className="inline-flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mb-6 animate-pulse">
                            <Brain className="h-12 w-12 text-white" />
                        </div>
                        <h1 className="text-3xl font-bold text-gray-900 mb-2">Processando sua Avaliação</h1>
                        <p className="text-gray-600">Nossa IA está analisando seu perfil vocacional...</p>
                    </div>

                    {/* Progress Bar */}
                    <Card className="mb-8">
                        <CardContent className="pt-6">
                            <div className="space-y-3">
                                <Progress value={progress} className="h-3" />
                                <div className="flex justify-between text-sm text-gray-600">
                                    <span>Progresso: {Math.round(progress)}%</span>
                                    <span>Tempo decorrido: {formatTime(elapsedTime)}</span>
                                </div>
                            </div>
                        </CardContent>
                    </Card>

                    {/* Processing Steps */}
                    <div className="space-y-4 mb-8">
                        {processingSteps.map((step, index) => {
                            const isCompleted = index < currentStep;
                            const isCurrent = index === currentStep;
                            const isPending = index > currentStep;

                            return (
                                <Card
                                    key={step.id}
                                    className={`transition-all duration-300 ${
                                        isCurrent
                                            ? 'border-blue-500 shadow-lg scale-105'
                                            : isCompleted
                                                ? 'border-green-500 bg-green-50'
                                                : 'opacity-50'
                                    }`}
                                >
                                    <CardContent className="flex items-start gap-4 p-4">
                                        <div className="flex-shrink-0 mt-1">
                                            {isCompleted ? (
                                                <CheckCircle className="h-6 w-6 text-green-600" />
                                            ) : isCurrent ? (
                                                <Loader2 className="h-6 w-6 text-blue-600 animate-spin" />
                                            ) : (
                                                <div className="h-6 w-6 rounded-full border-2 border-gray-300" />
                                            )}
                                        </div>
                                        <div className="flex-1">
                                            <h3
                                                className={`font-semibold mb-1 ${
                                                    isCompleted
                                                        ? 'text-green-700'
                                                        : isCurrent
                                                            ? 'text-blue-700'
                                                            : 'text-gray-500'
                                                }`}
                                            >
                                                {step.title}
                                            </h3>
                                            <p className="text-sm text-gray-600">{step.description}</p>
                                        </div>
                                    </CardContent>
                                </Card>
                            );
                        })}
                    </div>

                    {/* Fun Facts */}
                    <Card className="bg-gradient-to-r from-blue-50 to-purple-50 border-none">
                        <CardContent className="p-6 text-center">
                            <Sparkles className="h-8 w-8 text-purple-600 mx-auto mb-3" />
                            <p className="text-sm text-gray-700 italic">
                                "A escolha da carreira certa pode aumentar em até 80% sua satisfação profissional e
                                qualidade de vida."
                            </p>
                        </CardContent>
                    </Card>

                    {/* Footer Note */}
                    <p className="text-center text-xs text-gray-500 mt-6">
                        Este processo geralmente leva entre 15-30 segundos. Por favor, não feche esta página.
                    </p>
                </div>
            </div>
        </>
    );
}

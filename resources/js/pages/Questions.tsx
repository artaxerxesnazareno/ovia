import { Head, router, usePage } from '@inertiajs/react';
import { useState, useEffect, useCallback } from 'react';
import { ChevronLeft, ChevronRight, Save, Send, Compass, Star, Heart, User } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { RadioGroup, RadioGroupItem } from '@/components/ui/radio-group';
import { Textarea } from '@/components/ui/textarea';
import { Label } from '@/components/ui/label';
import { toast } from 'sonner';
import Header from '@/components/Header';
import { SharedData } from '@/types';

interface Question {
    id: number;
    category: string;
    dimension: string | null;
    text: string;
    type: 'likert' | 'multiple' | 'open';
    options: string[] | null;
    weight: number;
    order: number;
    is_required: boolean;
}

interface CategoryMetadata {
    title: string;
    description: string;
    icon: string;
}

interface QuestionsProps {
    assessment: {
        id: number;
        started_at: string;
    };
    questions: Record<string, Question[]>;
    savedResponses: Record<number, number>;
    savedTextResponses: Record<number, string>;
    categoryMetadata: Record<string, CategoryMetadata>;
}

const categoryIcons = {
    interests: Compass,
    skills: Star,
    values: Heart,
    personality: User,
};

const likertOptions = [
    { value: 1, label: 'Discordo Totalmente' },
    { value: 2, label: 'Discordo' },
    { value: 3, label: 'Neutro' },
    { value: 4, label: 'Concordo' },
    { value: 5, label: 'Concordo Totalmente' },
];

type AutoSaveStatus = 'idle' | 'saving' | 'saved' | 'error';

const getCsrfToken = (): string => {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
};

export default function Questions({
                                      assessment,
                                      questions,
                                      savedResponses,
                                      savedTextResponses,
                                      categoryMetadata,
                                  }: QuestionsProps) {
    const { auth } = usePage<SharedData>().props;

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

    const categories = Object.keys(questions);
    const [currentCategoryIndex, setCurrentCategoryIndex] = useState(() => {
        const firstIncompleteIndex = categories.findIndex((category) =>
            questions[category].some((question) => {
                if (question.type === 'open') {
                    return !savedTextResponses[question.id] || savedTextResponses[question.id].trim() === '';
                }

                return !savedResponses[question.id];
            })
        );

        return firstIncompleteIndex === -1 ? 0 : firstIncompleteIndex;
    });
    const [responses, setResponses] = useState<Record<number, number>>(savedResponses);
    const [textResponses, setTextResponses] = useState<Record<number, string>>(savedTextResponses);
    const [isSaving, setIsSaving] = useState(false);
    const [lastSaved, setLastSaved] = useState<Date | null>(null);
    const [autoSaveStatus, setAutoSaveStatus] = useState<AutoSaveStatus>('idle');
    const [autoSaveError, setAutoSaveError] = useState<string | null>(null);

    const currentCategory = categories[currentCategoryIndex];
    const currentQuestions = questions[currentCategory] || [];
    const totalQuestions = Object.values(questions).flat().length;
    const answeredQuestions = Object.keys({ ...responses, ...textResponses }).length;
    const progress = (answeredQuestions / totalQuestions) * 100;

    const autoSave = useCallback(async () => {
        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            const errorMessage = 'Token CSRF nao encontrado. Atualize a pagina e tente novamente.';
            setAutoSaveStatus('error');
            setAutoSaveError(errorMessage);
            toast.error(errorMessage);
            return;
        }

        setIsSaving(true);
        setAutoSaveStatus('saving');
        setAutoSaveError(null);

        try {
            const responsesArray = Object.entries({ ...responses, ...textResponses }).map(([questionId, value]) => ({
                question_id: parseInt(questionId),
                response_value: typeof value === 'number' ? value : null,
                response_text: typeof value === 'string' ? value : null,
            }));

            const res = await fetch(`/assessment/${assessment.id}/save`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ responses: responsesArray }),
            });

            const isJson = (res.headers.get('content-type') || '').includes('application/json');
            const data = isJson ? await res.json() : null;

            if (!res.ok || !data?.success) {
                if (res.status === 419) {
                    throw new Error('Sessao expirada. Recarregue a pagina e faca login novamente.');
                }

                throw new Error(data?.message || `Erro ao salvar respostas (HTTP ${res.status})`);
            }

            setLastSaved(data?.saved_at ? new Date(data.saved_at) : new Date());
            setAutoSaveStatus('saved');
        } catch (error) {
            const errorMessage = error instanceof Error
                ? error.message
                : 'Erro ao salvar respostas automaticamente';

            setAutoSaveStatus('error');
            setAutoSaveError(errorMessage);
            toast.error(errorMessage);
        } finally {
            setIsSaving(false);
        }
    }, [responses, textResponses, assessment.id]);

    // Auto-save com debounce para evitar perda ao trocar de tela/seção.
    useEffect(() => {
        const hasAnyResponse = Object.keys({ ...responses, ...textResponses }).length > 0;
        if (!hasAnyResponse) {
            return;
        }

        const timeoutId = window.setTimeout(() => {
            void autoSave();
        }, 1200);

        return () => {
            window.clearTimeout(timeoutId);
        };
    }, [responses, textResponses, autoSave]);

    const handleLikertResponse = (questionId: number, value: number) => {
        setResponses((prev) => ({ ...prev, [questionId]: value }));
    };

    const handleTextResponse = (questionId: number, value: string) => {
        setTextResponses((prev) => ({ ...prev, [questionId]: value }));
    };

    const handleMultipleResponse = (questionId: number, optionValue: number) => {
        setResponses((prev) => ({ ...prev, [questionId]: optionValue }));
    };

    const handleNextCategory = () => {
        if (currentCategoryIndex < categories.length - 1) {
            setCurrentCategoryIndex((prev) => prev + 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    const handlePreviousCategory = () => {
        if (currentCategoryIndex > 0) {
            setCurrentCategoryIndex((prev) => prev - 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    };

    const handleSubmit = async () => {
        // Validar questões obrigatórias
        const unansweredRequired = currentQuestions.filter((q) => {
            if (!q.is_required) return false;
            if (q.type === 'open') {
                return !textResponses[q.id] || textResponses[q.id].trim() === '';
            }
            return !responses[q.id];
        });

        if (unansweredRequired.length > 0) {
            toast.error(`Por favor, responda todas as ${unansweredRequired.length} questões obrigatórias`);
            return;
        }

        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            toast.error('Token CSRF nao encontrado. Atualize a pagina e tente novamente.');
            return;
        }

        setIsSaving(true);
        try {
            const responsesArray = Object.entries({ ...responses, ...textResponses }).map(([questionId, value]) => ({
                question_id: parseInt(questionId),
                response_value: typeof value === 'number' ? value : null,
                response_text: typeof value === 'string' ? value : null,
            }));

            const res = await fetch(`/assessment/${assessment.id}/submit`, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ responses: responsesArray }),
            });

            if (res.status === 419) {
                toast.error('Sessao expirada. Recarregue a pagina e faca login novamente.');
                return;
            }

            const data = await res.json();

            if (data.success) {
                toast.success('Avaliação enviada com sucesso!');
                setTimeout(() => {
                    router.visit(data.redirect_url);
                }, 1000);
            } else {
                toast.error(data.message || 'Erro ao enviar avaliação');
            }
        } catch (error) {
            toast.error('Erro ao enviar avaliação. Tente novamente.');
        } finally {
            setIsSaving(false);
        }
    };

    const CategoryIcon = categoryIcons[currentCategory as keyof typeof categoryIcons];
    const metadata = categoryMetadata[currentCategory];
    const isLastCategory = currentCategoryIndex === categories.length - 1;

    return (
        <>
            <Head title="Questionário de Avaliação" />

            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
                <Header currentView="assessment" onNavigate={handleHeaderNavigate} userProgress={0} user={auth.user} />
                <div className="container mx-auto max-w-4xl px-4 py-8">
                    {/* Progress Bar */}
                    <Card className="mb-6">
                        <CardContent className="pt-6">
                            <div className="space-y-2">
                                <div className="flex justify-between text-sm text-gray-600">
                                    <span>
                                        Progresso: {answeredQuestions} de {totalQuestions} questões
                                    </span>
                                    <span className="font-semibold">{Math.round(progress)}%</span>
                                </div>
                                <Progress value={progress} className="h-3" />
                                {autoSaveStatus === 'saving' && (
                                    <p className="text-xs text-blue-600 flex items-center gap-1">
                                        <Save className="h-3 w-3 animate-pulse" />
                                        Salvando respostas...
                                    </p>
                                )}
                                {autoSaveStatus === 'saved' && lastSaved && (
                                    <p className="text-xs text-gray-500 flex items-center gap-1">
                                        <Save className="h-3 w-3" />
                                        Salvo automaticamente às {lastSaved.toLocaleTimeString('pt-BR')}
                                    </p>
                                )}
                                {autoSaveStatus === 'error' && (
                                    <p className="text-xs text-red-600">
                                        Erro ao salvar automaticamente. {autoSaveError ?? 'Tente novamente.'}
                                    </p>
                                )}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Category Navigation */}
                    <div className="flex gap-2 mb-6 overflow-x-auto pb-2">
                        {categories.map((cat, index) => {
                            const Icon = categoryIcons[cat as keyof typeof categoryIcons];
                            const isActive = index === currentCategoryIndex;
                            const isCompleted = questions[cat].every((q) => {
                                if (q.type === 'open') return textResponses[q.id];
                                return responses[q.id];
                            });

                            return (
                                <button
                                    key={cat}
                                    onClick={() => setCurrentCategoryIndex(index)}
                                    className={`flex items-center gap-2 px-4 py-2 rounded-lg border-2 transition-all whitespace-nowrap ${
                                        isActive
                                            ? 'border-blue-500 bg-blue-50 text-blue-700'
                                            : isCompleted
                                                ? 'border-green-500 bg-green-50 text-green-700'
                                                : 'border-gray-200 bg-white text-gray-600 hover:border-gray-300'
                                    }`}
                                >
                                    <Icon className="h-4 w-4" />
                                    <span className="font-medium text-sm">{categoryMetadata[cat].title}</span>
                                    {isCompleted && <span className="text-xs">✓</span>}
                                </button>
                            );
                        })}
                    </div>

                    {/* Current Category */}
                    <Card className="mb-6">
                        <CardHeader className="bg-gradient-to-r from-blue-500 to-purple-500 text-white">
                            <div className="flex items-center gap-3">
                                <div className="p-2 bg-white/20 rounded-lg">
                                    <CategoryIcon className="h-6 w-6" />
                                </div>
                                <div>
                                    <CardTitle className="text-2xl">{metadata.title}</CardTitle>
                                    <CardDescription className="text-white/90">{metadata.description}</CardDescription>
                                </div>
                            </div>
                        </CardHeader>
                    </Card>

                    {/* Questions */}
                    <div className="space-y-6 mb-8">
                        {currentQuestions.map((question, index) => (
                            <Card key={question.id}>
                                <CardHeader>
                                    <CardTitle className="text-lg flex items-start gap-2">
                                        <span className="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-bold">
                                            {index + 1}
                                        </span>
                                        <span className="flex-1">
                                            {question.text}
                                            {question.is_required && <span className="text-red-500 ml-1">*</span>}
                                        </span>
                                    </CardTitle>
                                </CardHeader>
                                <CardContent>
                                    {question.type === 'likert' && (
                                        <RadioGroup
                                            value={responses[question.id]?.toString()}
                                            onValueChange={(value) => handleLikertResponse(question.id, parseInt(value))}
                                        >
                                            <div className="space-y-3">
                                                {likertOptions.map((option) => (
                                                    <div
                                                        key={option.value}
                                                        className="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
                                                    >
                                                        <RadioGroupItem value={option.value.toString()} id={`q${question.id}-${option.value}`} />
                                                        <Label
                                                            htmlFor={`q${question.id}-${option.value}`}
                                                            className="flex-1 cursor-pointer font-normal"
                                                        >
                                                            {option.label}
                                                        </Label>
                                                    </div>
                                                ))}
                                            </div>
                                        </RadioGroup>
                                    )}

                                    {question.type === 'multiple' && question.options && (
                                        <RadioGroup
                                            value={responses[question.id]?.toString()}
                                            onValueChange={(value) => handleMultipleResponse(question.id, parseInt(value))}
                                        >
                                            <div className="space-y-3">
                                                {question.options.map((option, optIndex) => (
                                                    <div
                                                        key={optIndex}
                                                        className="flex items-center space-x-3 p-3 rounded-lg hover:bg-gray-50 transition-colors"
                                                    >
                                                        <RadioGroupItem value={(optIndex + 1).toString()} id={`q${question.id}-opt${optIndex}`} />
                                                        <Label
                                                            htmlFor={`q${question.id}-opt${optIndex}`}
                                                            className="flex-1 cursor-pointer font-normal"
                                                        >
                                                            {option}
                                                        </Label>
                                                    </div>
                                                ))}
                                            </div>
                                        </RadioGroup>
                                    )}

                                    {question.type === 'open' && (
                                        <Textarea
                                            value={textResponses[question.id] || ''}
                                            onChange={(e) => handleTextResponse(question.id, e.target.value)}
                                            placeholder="Digite sua resposta aqui..."
                                            className="min-h-[120px] resize-none"
                                            maxLength={1000}
                                        />
                                    )}
                                </CardContent>
                            </Card>
                        ))}
                    </div>

                    {/* Navigation Buttons */}
                    <div className="flex justify-between items-center">
                        <Button
                            onClick={handlePreviousCategory}
                            variant="outline"
                            disabled={currentCategoryIndex === 0}
                            className="gap-2"
                        >
                            <ChevronLeft className="h-4 w-4" />
                            Anterior
                        </Button>

                        {isLastCategory ? (
                            <Button onClick={handleSubmit} disabled={isSaving} className="gap-2" size="lg">
                                <Send className="h-4 w-4" />
                                {isSaving ? 'Enviando...' : 'Enviar Avaliação'}
                            </Button>
                        ) : (
                            <Button onClick={handleNextCategory} className="gap-2">
                                Próxima Seção
                                <ChevronRight className="h-4 w-4" />
                            </Button>
                        )}
                    </div>
                </div>
            </div>
        </>
    );
}

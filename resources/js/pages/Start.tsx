import { Head, router, usePage } from '@inertiajs/react';
import { Clock, FileText, Compass, Star, Heart, User, AlertCircle } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Alert, AlertDescription } from '@/components/ui/alert';
import Header from '@/components/Header';
import { SharedData } from '@/types';

interface StartProps {
    hasExistingAssessment: boolean;
    existingAssessmentId: number | null;
    questionCounts: {
        interests: number;
        skills: number;
        values: number;
        personality: number;
    };
    totalQuestions: number;
    estimatedTime: number;
}

const categoryIcons = {
    interests: Compass,
    skills: Star,
    values: Heart,
    personality: User,
};

const categoryLabels = {
    interests: 'Interesses Profissionais',
    skills: 'Habilidades e Aptidões',
    values: 'Valores e Motivações',
    personality: 'Personalidade',
};

export default function Start({
                                  hasExistingAssessment,
                                  existingAssessmentId,
                                  questionCounts,
                                  totalQuestions,
                                  estimatedTime,
                              }: StartProps) {
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

    const handleStart = () => {
        router.post('/assessment/create');
    };

    const handleContinue = () => {
        router.visit(`/assessment/${existingAssessmentId}/questions`);
    };

    return (
        <>
            <Head title="Iniciar Avaliação Vocacional" />

            <div className="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50">
                <Header currentView="assessment" onNavigate={handleHeaderNavigate} userProgress={0} user={auth.user} />
                <div className="container mx-auto max-w-4xl px-4 py-12">
                    {/* Header */}
                    <div className="text-center mb-12">
                        <h1 className="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                            Avaliação Vocacional com IA
                        </h1>
                        <p className="text-lg text-gray-600 max-w-2xl mx-auto">
                            Descubra os cursos do IMETRO que melhor combinam com seu perfil através de uma análise
                            profunda baseada em inteligência artificial.
                        </p>
                    </div>

                    {/* Alerta de Avaliação Existente */}
                    {hasExistingAssessment && (
                        <Alert className="mb-8 border-blue-500 bg-blue-50">
                            <AlertCircle className="h-4 w-4 text-blue-600" />
                            <AlertDescription className="text-blue-900">
                                Você tem uma avaliação em andamento. Deseja continuar de onde parou?
                            </AlertDescription>
                        </Alert>
                    )}

                    {/* Informações da Avaliação */}
                    <div className="grid md:grid-cols-2 gap-6 mb-8">
                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <Clock className="h-5 w-5 text-blue-600" />
                                    Duração Estimada
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-3xl font-bold text-gray-900">{estimatedTime} min</p>
                                <p className="text-sm text-gray-600 mt-1">Tempo médio de conclusão</p>
                            </CardContent>
                        </Card>

                        <Card>
                            <CardHeader>
                                <CardTitle className="flex items-center gap-2">
                                    <FileText className="h-5 w-5 text-purple-600" />
                                    Total de Questões
                                </CardTitle>
                            </CardHeader>
                            <CardContent>
                                <p className="text-3xl font-bold text-gray-900">{totalQuestions}</p>
                                <p className="text-sm text-gray-600 mt-1">Distribuídas em 4 categorias</p>
                            </CardContent>
                        </Card>
                    </div>

                    {/* Seções da Avaliação */}
                    <Card className="mb-8">
                        <CardHeader>
                            <CardTitle>O que vamos avaliar?</CardTitle>
                            <CardDescription>
                                A avaliação é dividida em 4 seções principais para entender completamente seu perfil.
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                {Object.entries(questionCounts).map(([category, count]) => {
                                    const Icon = categoryIcons[category as keyof typeof categoryIcons];
                                    return (
                                        <div
                                            key={category}
                                            className="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-lg border border-gray-200 hover:border-gray-300 transition-colors"
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className="p-2 bg-blue-100 rounded-lg">
                                                    <Icon className="h-6 w-6 text-blue-600" />
                                                </div>
                                                <div>
                                                    <h3 className="font-semibold text-gray-900">
                                                        {categoryLabels[category as keyof typeof categoryLabels]}
                                                    </h3>
                                                    <p className="text-sm text-gray-600">{count} questões</p>
                                                </div>
                                            </div>
                                            <div className="text-right">
                                                <p className="text-sm text-gray-500">~{Math.ceil(count * 0.5)} min</p>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </CardContent>
                    </Card>

                    {/* Como Funciona */}
                    <Card className="mb-8">
                        <CardHeader>
                            <CardTitle>Como funciona?</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <ol className="space-y-3">
                                <li className="flex gap-3">
                                    <span className="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        1
                                    </span>
                                    <div>
                                        <strong className="text-gray-900">Responda o questionário</strong>
                                        <p className="text-sm text-gray-600">
                                            Preencha honestamente todas as questões sobre seus interesses, habilidades,
                                            valores e personalidade.
                                        </p>
                                    </div>
                                </li>
                                <li className="flex gap-3">
                                    <span className="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        2
                                    </span>
                                    <div>
                                        <strong className="text-gray-900">Processamento por IA</strong>
                                        <p className="text-sm text-gray-600">
                                            Nossa inteligência artificial analisa suas respostas e compara com os perfis
                                            de todos os cursos do IMETRO.
                                        </p>
                                    </div>
                                </li>
                                <li className="flex gap-3">
                                    <span className="flex-shrink-0 w-6 h-6 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-bold">
                                        3
                                    </span>
                                    <div>
                                        <strong className="text-gray-900">Receba suas recomendações</strong>
                                        <p className="text-sm text-gray-600">
                                            Veja os 3 cursos mais compatíveis com seu perfil, saídas profissionais e um
                                            roadmap personalizado.
                                        </p>
                                    </div>
                                </li>
                            </ol>
                        </CardContent>
                    </Card>

                    {/* Dicas Importantes */}
                    <Alert className="mb-8 bg-amber-50 border-amber-200">
                        <AlertCircle className="h-4 w-4 text-amber-600" />
                        <AlertDescription className="text-amber-900">
                            <strong>Dicas importantes:</strong>
                            <ul className="list-disc list-inside mt-2 space-y-1 text-sm">
                                <li>Responda com honestidade, não há respostas certas ou erradas</li>
                                <li>Suas respostas são salvas automaticamente enquanto você responde</li>
                                <li>Você pode pausar e continuar depois a qualquer momento</li>
                                <li>Reserve um tempo sem interrupções para melhores resultados</li>
                            </ul>
                        </AlertDescription>
                    </Alert>

                    {/* Botões de Ação */}
                    <div className="flex flex-col sm:flex-row gap-4 justify-center">
                        {hasExistingAssessment ? (
                            <>
                                <Button onClick={handleContinue} size="lg" className="text-lg px-8 py-6">
                                    Continuar Avaliação
                                </Button>
                                <Button onClick={handleStart} variant="outline" size="lg" className="text-lg px-8 py-6">
                                    Começar Nova Avaliação
                                </Button>
                            </>
                        ) : (
                            <Button onClick={handleStart} size="lg" className="text-lg px-8 py-6">
                                Iniciar Avaliação
                            </Button>
                        )}
                    </div>

                    {/* Footer Note */}
                    <p className="text-center text-sm text-gray-500 mt-8">
                        Ao iniciar, você concorda que seus dados serão processados de acordo com a nossa Política de
                        Privacidade (LGPD).
                    </p>
                </div>
            </div>
        </>
    );
}

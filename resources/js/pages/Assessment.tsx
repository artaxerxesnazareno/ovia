import { useState } from 'react';
import { Head, Link } from '@inertiajs/react';
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Progress } from "@/components/ui/progress";
import { Badge } from "@/components/ui/badge";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Label } from "@/components/ui/label";
import { AppHeader } from '@/components/app-header';
import { AppSidebar } from '@/components/app-sidebar';

// Mock Data Structure
const sections = [
    {
        id: 'interests',
        title: 'Interesses Profissionais',
        description: 'Avalie o quanto você se interessa por cada atividade (1 = Nada, 5 = Muito)',
        questions: [
            { id: 1, text: 'Resolver problemas lógicos e matemáticos' },
            { id: 2, text: 'Ajudar pessoas a resolverem seus conflitos' },
            { id: 3, text: 'Criar designs e ilustrações artísticas' },
        ]
    },
    {
        id: 'skills',
        title: 'Habilidades e Aptidões',
        description: 'Avalie sua facilidade com cada tarefa (1 = Difícil, 5 = Fácil)',
        questions: [
            { id: 4, text: 'Aprender novas tecnologias rapidamente' },
            { id: 5, text: 'Falar em público e apresentar ideias' },
            { id: 6, text: 'Organizar tarefas e gerenciar tempo' },
        ]
    },
    {
        id: 'values',
        title: 'Valores e Motivações',
        description: 'O quanto isso é importante para você? (1 = Pouco, 5 = Muito)',
        questions: [
            { id: 7, text: 'Ter estabilidade financeira e segurança' },
            { id: 8, text: 'Trabalhar em projetos que impactam a sociedade' },
            { id: 9, text: 'Ter liberdade e autonomia no dia a dia' },
        ]
    },
    {
        id: 'personality',
        title: 'Personalidade',
        description: 'O quanto essa frase descreve você? (1 = Discordo, 5 = Concordo)',
        questions: [
            { id: 10, text: 'Prefiro trabalhar em equipe do que sozinho' },
            { id: 11, text: 'Gosto de planejar tudo com antecedência' },
            { id: 12, text: 'Tomo decisões baseadas mais na lógica que na emoção' },
        ]
    }
];

export default function Assessment() {
    const [currentStep, setCurrentStep] = useState<'intro' | number | 'completed'>('intro');
    const [responses, setResponses] = useState<Record<number, number>>({});

    const handleStart = () => setCurrentStep(0);

    const handleAnswer = (questionId: number, value: number) => {
        setResponses(prev => ({ ...prev, [questionId]: value }));
    };

    const handleNext = () => {
        if (typeof currentStep === 'number') {
            if (currentStep < sections.length - 1) {
                setCurrentStep(currentStep + 1);
            } else {
                setCurrentStep('completed');
            }
        }
    };

    const handlePrevious = () => {
        if (typeof currentStep === 'number' && currentStep > 0) {
            setCurrentStep(currentStep - 1);
        }
    };

    const totalQuestions = sections.reduce((acc, section) => acc + section.questions.length, 0);
    const answeredCount = Object.keys(responses).length;
    const progress = Math.round((answeredCount / totalQuestions) * 100);

    const renderIntro = () => (
        <div className="max-w-2xl mx-auto space-y-8 py-12 text-center">
            <h1 className="text-4xl font-bold">
                Avaliação Vocacional
                <span className="text-gradient block mt-2">Descubra seu Potencial</span>
            </h1>
            <p className="text-xl text-slate-600">
                Esta avaliação ajudará a identificar as carreiras mais compatíveis com seu perfil,
                interesses e habilidades. O processo leva cerca de 10-15 minutos.
            </p>
            <div className="grid grid-cols-2 gap-4 text-left max-w-lg mx-auto bg-white p-6 rounded-xl shadow-sm border">
                <div className="flex items-center space-x-2">
                    <span className="text-2xl">🎯</span>
                    <span>Análise de Interesses</span>
                </div>
                <div className="flex items-center space-x-2">
                    <span className="text-2xl">🧠</span>
                    <span>Mapeamento de Skills</span>
                </div>
                <div className="flex items-center space-x-2">
                    <span className="text-2xl">💎</span>
                    <span>Valores Pessoais</span>
                </div>
                <div className="flex items-center space-x-2">
                    <span className="text-2xl">🧩</span>
                    <span>Perfil Comportamental</span>
                </div>
            </div>
            <Button onClick={handleStart} size="lg" className="btn-gradient px-8 text-lg h-12">
                Iniciar Avaliação
            </Button>
        </div>
    );

    const renderSection = (sectionIndex: number) => {
        const section = sections[sectionIndex];
        const isSectionComplete = section.questions.every(q => responses[q.id]);

        return (
            <div className="max-w-3xl mx-auto space-y-8 py-8">
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-2xl font-bold">{section.title}</h2>
                        <p className="text-slate-600">{section.description}</p>
                    </div>
                    <Badge variant="secondary" className="text-md px-3 py-1">
                        Seção {sectionIndex + 1} de {sections.length}
                    </Badge>
                </div>

                <div className="space-y-6">
                    {section.questions.map((question) => (
                        <Card key={question.id} className="border-slate-200 shadow-sm hover:shadow-md transition-shadow">
                            <CardContent className="p-6">
                                <div className="space-y-4">
                                    <h3 className="font-medium text-lg leading-relaxed">{question.text}</h3>
                                    <RadioGroup
                                        value={responses[question.id]?.toString()}
                                        onValueChange={(val) => handleAnswer(question.id, parseInt(val))}
                                        className="flex justify-between items-center pt-2"
                                    >
                                        {[1, 2, 3, 4, 5].map((val) => (
                                            <div key={val} className="flex flex-col items-center space-y-2 cursor-pointer group">
                                                <RadioGroupItem value={val.toString()} id={`q${question.id}-${val}`} className="peer sr-only" />
                                                <Label
                                                    htmlFor={`q${question.id}-${val}`}
                                                    className={`
                                                        w-10 h-10 rounded-full flex items-center justify-center border-2 text-lg font-medium transition-all cursor-pointer
                                                        ${responses[question.id] === val
                                                            ? 'bg-vocia-600 border-vocia-600 text-white scale-110 shadow-lg'
                                                            : 'bg-white border-slate-300 text-slate-500 hover:border-vocia-400 hover:bg-vocia-50'
                                                        }
                                                    `}
                                                >
                                                    {val}
                                                </Label>
                                                <span className="text-xs text-slate-400 font-medium">
                                                    {val === 1 ? 'Discordo' : val === 5 ? 'Concordo' : ''}
                                                </span>
                                            </div>
                                        ))}
                                    </RadioGroup>
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>

                <div className="flex justify-between pt-6">
                    <Button
                        variant="outline"
                        onClick={handlePrevious}
                        disabled={sectionIndex === 0}
                    >
                        ← Anterior
                    </Button>
                    <Button
                        onClick={handleNext}
                        disabled={!isSectionComplete}
                        className="btn-gradient px-8"
                    >
                        {sectionIndex === sections.length - 1 ? 'Finalizar' : 'Próxima Seção →'}
                    </Button>
                </div>
            </div>
        );
    };

    const renderCompleted = () => (
        <div className="max-w-xl mx-auto py-16 text-center space-y-6">
            <div className="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto text-4xl mb-6">
                ✓
            </div>
            <h2 className="text-3xl font-bold">Avaliação Concluída!</h2>
            <p className="text-slate-600 text-lg">
                Obrigado por completar a avaliação. Nossa Inteligência Artificial está analisando suas respostas para gerar seu perfil personalizado.
            </p>
            <Card className="bg-slate-50 border-dashed">
                <CardContent className="pt-6">
                    <div className="flex justify-between items-center text-sm text-slate-500 mb-2">
                        <span>Analisando perfil...</span>
                        <span>100%</span>
                    </div>
                    <Progress value={100} className="h-2" />
                </CardContent>
            </Card>
            <div className="pt-8">
                <Link href={route('app.dashboard')}>
                    <Button size="lg" className="btn-gradient w-full">
                        Ver Resultados no Dashboard
                    </Button>
                </Link>
            </div>
        </div>
    );

    return (
        <div className="flex min-h-screen bg-slate-50/50">
            <Head title="Avaliação Vocacional - Ovia" />
            {/* Note: In a real implementation we might want a simplified Layout for distraction-free assessment */}

            <main className="flex-1 w-full flex flex-col">
                <AppHeader breadcrumbs={[{ title: 'Avaliação Vocacional', href: route('assessment.start') }]} />

                <div className="p-6 md:p-10 flex-1 overflow-y-auto">
                    {typeof currentStep === 'number' && (
                        <div className="max-w-3xl mx-auto mb-8">
                            <div className="flex justify-between text-sm font-medium text-slate-500 mb-2">
                                <span>Progresso Geral</span>
                                <span>{progress}%</span>
                            </div>
                            <Progress value={progress} className="h-2" />
                        </div>
                    )}

                    {currentStep === 'intro' && renderIntro()}
                    {typeof currentStep === 'number' && renderSection(currentStep)}
                    {currentStep === 'completed' && renderCompleted()}
                </div>
            </main>
        </div>
    );
}

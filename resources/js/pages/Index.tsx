import Assessment from '@/components/Assessment';
import Chatbot from '@/components/Chatbot';
import Dashboard, { type DashboardData } from '@/components/Dashboard';
import Features from '@/components/Features';
import Header from '@/components/Header';
import Hero from '@/components/Hero';
import { useState } from 'react';

import { SharedData } from '@/types';
import { router, usePage } from '@inertiajs/react';

const Index = ({ initialView = 'home', dashboardData }: { initialView?: string; dashboardData?: DashboardData }) => {
    const { auth } = usePage<SharedData>().props;
    const [currentView, setCurrentView] = useState(initialView);
    const [userProgress, setUserProgress] = useState(0);

    const handleNavigate = (view: string) => {
        if (view === 'assessment') {
            router.visit('/assessment/start');
            return;
        }

        if (view === 'dashboard' && auth.user) {
            router.visit('/app-dashboard');
            return;
        }

        setCurrentView(view);
    };

    const renderCurrentView = () => {
        switch (currentView) {
            case 'dashboard':
                return <Dashboard onStartAssessment={() => router.visit('/assessment/start')} data={dashboardData} />;
            case 'assessment':
                return (
                    <Assessment
                        onComplete={() => {
                            setUserProgress(100);
                            router.visit('/app-dashboard');
                        }}
                    />
                );
            default:
                return (
                    <>
                        <Hero
                            onGetStarted={() => {
                                if (auth.user) {
                                    router.visit('/app-dashboard');
                                    return;
                                }

                                setCurrentView('dashboard');
                            }}
                        />
                        <Features />
                    </>
                );
        }
    };

    return (
        <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50">
            <Header currentView={currentView} onNavigate={handleNavigate} userProgress={userProgress} user={auth.user} />

            <main className="relative">{renderCurrentView()}</main>

            <Chatbot />

            <footer className="mt-20 bg-slate-900 py-12 text-white">
                <div className="container mx-auto px-4">
                    <div className="grid grid-cols-1 gap-8 md:grid-cols-4">
                        <div>
                            <h3 className="text-gradient mb-4 text-2xl font-bold">OVIA</h3>
                            <p className="text-slate-300">Plataforma de orientação vocacional baseada em inteligência artificial.</p>
                        </div>
                        <div>
                            <h4 className="mb-4 font-semibold">Recursos</h4>
                            <ul className="space-y-2 text-slate-300">
                                <li>Testes Vocacionais</li>
                                <li>Análise de Perfil</li>
                                <li>Recomendações IA</li>
                                <li>Orientação Especializada</li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="mb-4 font-semibold">Suporte</h4>
                            <ul className="space-y-2 text-slate-300">
                                <li>Central de Ajuda</li>
                                <li>FAQ</li>
                                <li>Contato</li>
                                <li>Documentação</li>
                            </ul>
                        </div>
                        <div>
                            <h4 className="mb-4 font-semibold">Legal</h4>
                            <ul className="space-y-2 text-slate-300">
                                <li>Termos de Uso</li>
                                <li>Política de Privacidade</li>
                                <li>LGPD</li>
                                <li>Cookies</li>
                            </ul>
                        </div>
                    </div>
                    <div className="mt-8 border-t border-slate-700 pt-8 text-center text-slate-400">
                        <p>&copy; 2026 OVIA. Todos os direitos reservados.</p>
                    </div>
                </div>
            </footer>
        </div>
    );
};

export default Index;

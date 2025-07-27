
import { useState } from 'react';
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Progress } from "@/components/ui/progress";
import Header from '@/components/Header';
import Hero from '@/components/Hero';
import Features from '@/components/Features';
import Chatbot from '@/components/Chatbot';
import Dashboard from '@/components/Dashboard';
import Assessment from '@/components/Assessment';

const Index = () => {
  const [currentView, setCurrentView] = useState('home');
  const [userProgress, setUserProgress] = useState(0);

  const renderCurrentView = () => {
    switch (currentView) {
      case 'dashboard':
        return <Dashboard onStartAssessment={() => setCurrentView('assessment')} />;
      case 'assessment':
        return <Assessment onComplete={() => {
          setUserProgress(100);
          setCurrentView('dashboard');
        }} />;
      default:
        return (
          <>
            <Hero onGetStarted={() => setCurrentView('dashboard')} />
            <Features />
          </>
        );
    }
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-purple-50">
      <Header
        currentView={currentView}
        onNavigate={setCurrentView}
        userProgress={userProgress}
      />

      <main className="relative">
        {renderCurrentView()}
      </main>

      <Chatbot />

      <footer className="bg-slate-900 text-white py-12 mt-20">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div>
              <h3 className="text-2xl font-bold text-gradient mb-4">OVIA</h3>
              <p className="text-slate-300">
                Plataforma de orientação vocacional baseada em inteligência artificial.
              </p>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Recursos</h4>
              <ul className="space-y-2 text-slate-300">
                <li>Testes Vocacionais</li>
                <li>Análise de Perfil</li>
                <li>Recomendações IA</li>
                <li>Orientação Especializada</li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Suporte</h4>
              <ul className="space-y-2 text-slate-300">
                <li>Central de Ajuda</li>
                <li>FAQ</li>
                <li>Contato</li>
                <li>Documentação</li>
              </ul>
            </div>
            <div>
              <h4 className="font-semibold mb-4">Legal</h4>
              <ul className="space-y-2 text-slate-300">
                <li>Termos de Uso</li>
                <li>Política de Privacidade</li>
                <li>LGPD</li>
                <li>Cookies</li>
              </ul>
            </div>
          </div>
          <div className="border-t border-slate-700 mt-8 pt-8 text-center text-slate-400">
            <p>&copy; 2024 VocIA. Todos os direitos reservados.</p>
          </div>
        </div>
      </footer>
    </div>
  );
};

export default Index;

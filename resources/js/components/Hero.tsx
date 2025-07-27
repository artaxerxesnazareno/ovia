
import { Button } from "@/components/ui/button";
import { Card, CardContent } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

interface HeroProps {
  onGetStarted: () => void;
}

const Hero = ({ onGetStarted }: HeroProps) => {
  return (
    <section className="relative py-20 overflow-hidden">
      <div className="absolute inset-0 bg-gradient-to-br from-vocia-500/10 via-purple-500/5 to-transparent"></div>
      
      <div className="container mx-auto px-4 relative">
        <div className="grid lg:grid-cols-2 gap-12 items-center">
          <div className="space-y-8 animate-fade-in">
            <div className="space-y-4">
              <Badge className="bg-gradient-vocia text-white px-4 py-2">
                🚀 Powered by AI
              </Badge>
              
              <h1 className="text-5xl lg:text-6xl font-bold leading-tight">
                Descubra sua
                <span className="text-gradient block">
                  carreira ideal
                </span>
                com inteligência artificial
              </h1>
              
              <p className="text-xl text-slate-600 leading-relaxed max-w-lg">
                Nossa plataforma revolucionária combina testes vocacionais avançados 
                com IA para revelar o caminho profissional perfeito para você.
              </p>
            </div>

            <div className="flex flex-col sm:flex-row gap-4">
              <Button 
                onClick={onGetStarted}
                className="btn-gradient text-lg py-4 px-8"
                size="lg"
              >
                Iniciar Orientação Vocacional
              </Button>
              
              <Button 
                variant="outline" 
                size="lg"
                className="border-2 hover:bg-slate-50"
              >
                Ver Demonstração
              </Button>
            </div>

            <div className="flex items-center space-x-8 pt-8">
              <div className="text-center">
                <div className="text-3xl font-bold text-vocia-600">25k+</div>
                <div className="text-sm text-slate-500">Usuários ativos</div>
              </div>
              <div className="text-center">
                <div className="text-3xl font-bold text-purple-600">95%</div>
                <div className="text-sm text-slate-500">Taxa de satisfação</div>
              </div>
              <div className="text-center">
                <div className="text-3xl font-bold text-vocia-600">500+</div>
                <div className="text-sm text-slate-500">Carreiras mapeadas</div>
              </div>
            </div>
          </div>

          <div className="relative animate-slide-in-right">
            <div className="absolute inset-0 bg-gradient-vocia rounded-3xl blur-3xl opacity-20 animate-pulse-slow"></div>
            
            <Card className="relative card-gradient p-8 transform rotate-2 hover:rotate-0 transition-transform duration-500">
              <CardContent className="space-y-6">
                <div className="flex items-center space-x-3">
                  <div className="w-4 h-4 bg-green-500 rounded-full animate-pulse"></div>
                  <span className="font-semibold">IA Analisando...</span>
                </div>
                
                <div className="space-y-4">
                  <div className="bg-white/60 rounded-lg p-4">
                    <div className="text-sm text-slate-600 mb-2">Área de Interesse Detectada:</div>
                    <div className="font-semibold text-vocia-700">Tecnologia & Inovação</div>
                  </div>
                  
                  <div className="bg-white/60 rounded-lg p-4">
                    <div className="text-sm text-slate-600 mb-2">Compatibilidade:</div>
                    <div className="flex items-center space-x-2">
                      <div className="flex-1 bg-slate-200 rounded-full h-2">
                        <div className="bg-gradient-vocia h-2 rounded-full w-4/5"></div>
                      </div>
                      <span className="font-semibold text-vocia-700">92%</span>
                    </div>
                  </div>
                  
                  <div className="bg-white/60 rounded-lg p-4">
                    <div className="text-sm text-slate-600 mb-2">Recomendação Principal:</div>
                    <div className="font-semibold text-purple-700">Engenharia de Software</div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </section>
  );
};

export default Hero;

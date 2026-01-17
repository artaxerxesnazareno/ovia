
import { Button } from "@/components/ui/button";
import { Progress } from "@/components/ui/progress";
import { Badge } from "@/components/ui/badge";

interface HeaderProps {
  currentView: string;
  onNavigate: (view: string) => void;
  userProgress: number;
}

const Header = ({ currentView, onNavigate, userProgress }: HeaderProps) => {
  return (
    <header className="bg-white/80 backdrop-blur-md border-b border-white/20 sticky top-0 z-50">
      <div className="container mx-auto px-4 py-4">
        <div className="flex items-center justify-between">
          <div className="flex items-center space-x-8">
            <h1
              className="text-3xl font-bold text-gradient cursor-pointer"
              onClick={() => onNavigate('home')}
            >
              OVIA
            </h1>

            <nav className="hidden md:flex space-x-6">
              <Button
                variant={currentView === 'home' ? 'default' : 'ghost'}
                onClick={() => onNavigate('home')}
                className="font-medium"
              >
                Início
              </Button>
              <Button
                variant={currentView === 'dashboard' ? 'default' : 'ghost'}
                onClick={() => onNavigate('dashboard')}
                className="font-medium"
              >
                Dashboard
              </Button>
              <Button
                variant={currentView === 'assessment' ? 'default' : 'ghost'}
                onClick={() => onNavigate('assessment')}
                className="font-medium"
              >
                Avaliações
              </Button>
            </nav>
          </div>

          <div className="flex items-center space-x-4">
            {userProgress > 0 && (
              <div className="hidden sm:flex items-center space-x-2">
                <span className="text-sm text-slate-600">Progresso:</span>
                <Progress value={userProgress} className="w-24" />
                <Badge variant="secondary">{userProgress}%</Badge>
              </div>
            )}

            <Button className="btn-gradient">
              Entrar
            </Button>
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;

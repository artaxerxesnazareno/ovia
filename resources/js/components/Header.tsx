
import { Button } from "@/components/ui/button";
import { Progress } from "@/components/ui/progress";
import { Badge } from "@/components/ui/badge";
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from "@/components/ui/dropdown-menu";
import { Avatar, AvatarFallback, AvatarImage } from "@/components/ui/avatar";
import { UserMenuContent } from "@/components/user-menu-content";
import { useInitials } from "@/hooks/use-initials";
import { type User } from "@/types";
import { Link } from "@inertiajs/react";

interface HeaderProps {
  currentView: string;
  onNavigate: (view: string) => void;
  userProgress: number;
  user?: User;
}

const Header = ({ currentView, onNavigate, userProgress, user }: HeaderProps) => {
  const getInitials = useInitials();
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
              {user && (
                <>
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
                </>
              )}
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

            {user ? (
              <DropdownMenu>
                <DropdownMenuTrigger asChild>
                  <Button variant="ghost" className="size-10 rounded-full p-1">
                    <Avatar className="size-8 overflow-hidden rounded-full">
                      <AvatarImage src={user.avatar} alt={user.name} />
                      <AvatarFallback className="rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                        {getInitials(user.name)}
                      </AvatarFallback>
                    </Avatar>
                  </Button>
                </DropdownMenuTrigger>
                <DropdownMenuContent className="w-56" align="end">
                  <UserMenuContent user={user} />
                </DropdownMenuContent>
              </DropdownMenu>
            ) : (
              <Link href={route('login')}>
                <Button className="btn-gradient">
                  Entrar
                </Button>
              </Link>
            )}
          </div>
        </div>
      </div>
    </header>
  );
};

export default Header;

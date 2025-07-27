
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Progress } from "@/components/ui/progress";
import { Badge } from "@/components/ui/badge";

interface DashboardProps {
  onStartAssessment: () => void;
}

const Dashboard = ({ onStartAssessment }: DashboardProps) => {
  const assessments = [
    {
      title: "Teste de Interesses Profissionais",
      description: "Identifique suas áreas de interesse e paixões profissionais",
      progress: 100,
      status: "Concluído",
      score: 85,
      duration: "15 min"
    },
    {
      title: "Análise de Inteligências Múltiplas",
      description: "Descubra suas diferentes formas de inteligência",
      progress: 60,
      status: "Em Progresso",
      score: null,
      duration: "20 min"
    },
    {
      title: "Perfil Comportamental",
      description: "Entenda seu estilo de trabalho e preferências",
      progress: 0,
      status: "Não Iniciado",
      score: null,
      duration: "12 min"
    },
    {
      title: "Teste Gosto e Faço",
      description: "Alinhe suas preferências com suas habilidades",
      progress: 0,
      status: "Não Iniciado",
      score: null,
      duration: "18 min"
    }
  ];

  const careerRecommendations = [
    {
      career: "Engenharia de Software",
      match: 92,
      salary: "R$ 8.500 - R$ 15.000",
      growth: "Alta",
      description: "Desenvolvimento de sistemas e aplicações"
    },
    {
      career: "Cientista de Dados",
      match: 88,
      salary: "R$ 9.000 - R$ 18.000",
      growth: "Muito Alta",
      description: "Análise e interpretação de dados complexos"
    },
    {
      career: "UX/UI Designer",
      match: 82,
      salary: "R$ 5.500 - R$ 12.000",
      growth: "Alta",
      description: "Design de experiências digitais"
    }
  ];

  const overallProgress = Math.round(assessments.reduce((acc, curr) => acc + curr.progress, 0) / assessments.length);

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="space-y-8">
        {/* Header */}
        <div className="text-center space-y-4">
          <h1 className="text-4xl font-bold">
            Seu Dashboard de
            <span className="text-gradient block">Orientação Vocacional</span>
          </h1>
          <p className="text-xl text-slate-600">
            Acompanhe seu progresso e descubra suas recomendações personalizadas
          </p>
        </div>

        {/* Progress Overview */}
        <Card className="card-gradient">
          <CardHeader>
            <CardTitle className="flex items-center justify-between">
              <span>Progresso Geral</span>
              <Badge variant={overallProgress === 100 ? "default" : "secondary"}>
                {overallProgress}% Completo
              </Badge>
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              <Progress value={overallProgress} className="h-3" />
              <div className="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <div>
                  <div className="text-2xl font-bold text-vocia-600">
                    {assessments.filter(a => a.status === "Concluído").length}
                  </div>
                  <div className="text-sm text-slate-500">Testes Concluídos</div>
                </div>
                <div>
                  <div className="text-2xl font-bold text-purple-600">
                    {careerRecommendations.length}
                  </div>
                  <div className="text-sm text-slate-500">Carreiras Sugeridas</div>
                </div>
                <div>
                  <div className="text-2xl font-bold text-vocia-600">
                    {Math.max(...assessments.map(a => a.score || 0))}
                  </div>
                  <div className="text-sm text-slate-500">Melhor Score</div>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>

        <div className="grid lg:grid-cols-2 gap-8">
          {/* Assessments */}
          <div className="space-y-6">
            <h2 className="text-2xl font-bold">Avaliações Disponíveis</h2>
            
            {assessments.map((assessment, index) => (
              <Card key={index} className="card-gradient hover:shadow-lg transition-shadow">
                <CardHeader>
                  <div className="flex items-start justify-between">
                    <div className="space-y-2">
                      <CardTitle className="text-lg">{assessment.title}</CardTitle>
                      <CardDescription>{assessment.description}</CardDescription>
                      <div className="flex items-center space-x-2">
                        <Badge variant="outline">⏱️ {assessment.duration}</Badge>
                        {assessment.score && (
                          <Badge className="bg-green-100 text-green-800">
                            Score: {assessment.score}
                          </Badge>
                        )}
                      </div>
                    </div>
                    <Badge 
                      variant={assessment.status === "Concluído" ? "default" : 
                               assessment.status === "Em Progresso" ? "secondary" : "outline"}
                    >
                      {assessment.status}
                    </Badge>
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    <div className="space-y-2">
                      <div className="flex justify-between text-sm">
                        <span>Progresso</span>
                        <span>{assessment.progress}%</span>
                      </div>
                      <Progress value={assessment.progress} />
                    </div>
                    
                    <Button 
                      onClick={onStartAssessment}
                      className={assessment.status === "Concluído" ? "w-full" : "w-full btn-gradient"}
                      variant={assessment.status === "Concluído" ? "outline" : "default"}
                    >
                      {assessment.status === "Concluído" ? "Ver Resultados" : 
                       assessment.status === "Em Progresso" ? "Continuar" : "Iniciar Teste"}
                    </Button>
                  </div>
                </CardContent>
              </Card>
            ))}
          </div>

          {/* Career Recommendations */}
          <div className="space-y-6">
            <h2 className="text-2xl font-bold">Recomendações de Carreira</h2>
            
            {careerRecommendations.map((career, index) => (
              <Card key={index} className="card-gradient hover:shadow-lg transition-shadow">
                <CardHeader>
                  <div className="flex items-start justify-between">
                    <div className="space-y-2">
                      <CardTitle className="text-lg">{career.career}</CardTitle>
                      <CardDescription>{career.description}</CardDescription>
                    </div>
                    <div className="text-center">
                      <div className="text-2xl font-bold text-vocia-600">{career.match}%</div>
                      <div className="text-xs text-slate-500">Match</div>
                    </div>
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    <div className="grid grid-cols-2 gap-4 text-sm">
                      <div>
                        <span className="text-slate-500">Salário:</span>
                        <div className="font-semibold">{career.salary}</div>
                      </div>
                      <div>
                        <span className="text-slate-500">Crescimento:</span>
                        <div className="font-semibold text-green-600">{career.growth}</div>
                      </div>
                    </div>
                    
                    <Progress value={career.match} className="h-2" />
                    
                    <Button variant="outline" className="w-full">
                      Explorar Carreira
                    </Button>
                  </div>
                </CardContent>
              </Card>
            ))}

            <Card className="card-gradient border-2 border-vocia-200">
              <CardContent className="text-center py-8">
                <div className="space-y-4">
                  <div className="text-4xl">🎯</div>
                  <h3 className="text-xl font-bold">Quer mais recomendações?</h3>
                  <p className="text-slate-600">
                    Complete mais testes para receber sugestões ainda mais precisas
                  </p>
                  <Button onClick={onStartAssessment} className="btn-gradient">
                    Fazer Mais Testes
                  </Button>
                </div>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    </div>
  );
};

export default Dashboard;

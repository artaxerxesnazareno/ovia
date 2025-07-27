
import { useState } from 'react';
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Progress } from "@/components/ui/progress";
import { Badge } from "@/components/ui/badge";
import { RadioGroup, RadioGroupItem } from "@/components/ui/radio-group";
import { Label } from "@/components/ui/label";

interface AssessmentProps {
  onComplete: () => void;
}

const Assessment = ({ onComplete }: AssessmentProps) => {
  const [currentQuestion, setCurrentQuestion] = useState(0);
  const [answers, setAnswers] = useState<Record<number, string>>({});
  const [showResults, setShowResults] = useState(false);

  const questions = [
    {
      id: 1,
      category: "Interesses Profissionais",
      question: "Qual dessas atividades mais desperta seu interesse?",
      options: [
        { value: "tech", label: "Resolver problemas complexos com tecnologia" },
        { value: "people", label: "Ajudar e orientar outras pessoas" },
        { value: "creative", label: "Criar e desenvolver projetos artísticos" },
        { value: "business", label: "Liderar equipes e tomar decisões estratégicas" }
      ]
    },
    {
      id: 2,
      category: "Inteligências Múltiplas",
      question: "Em qual situação você se sente mais confortável?",
      options: [
        { value: "logical", label: "Analisando dados e padrões numéricos" },
        { value: "linguistic", label: "Escrevendo textos ou fazendo apresentações" },
        { value: "spatial", label: "Trabalhando com imagens e design visual" },
        { value: "interpersonal", label: "Colaborando em equipe e resolvendo conflitos" }
      ]
    },
    {
      id: 3,
      category: "Perfil Comportamental",
      question: "Como você prefere trabalhar?",
      options: [
        { value: "independent", label: "De forma independente, com autonomia total" },
        { value: "structured", label: "Seguindo processos e estruturas bem definidas" },
        { value: "collaborative", label: "Em equipe, compartilhando ideias constantemente" },
        { value: "flexible", label: "Com flexibilidade e mudanças constantes" }
      ]
    },
    {
      id: 4,
      category: "Valores Profissionais",
      question: "O que é mais importante para você no trabalho?",
      options: [
        { value: "impact", label: "Causar impacto positivo na sociedade" },
        { value: "innovation", label: "Inovar e criar coisas novas" },
        { value: "stability", label: "Ter estabilidade e segurança financeira" },
        { value: "growth", label: "Crescimento pessoal e profissional contínuo" }
      ]
    },
    {
      id: 5,
      category: "Ambiente de Trabalho",
      question: "Qual ambiente de trabalho é ideal para você?",
      options: [
        { value: "office", label: "Escritório tradicional com colegas próximos" },
        { value: "remote", label: "Home office com flexibilidade total" },
        { value: "field", label: "Trabalho de campo, sempre em lugares diferentes" },
        { value: "creative", label: "Espaços criativos e descontraídos" }
      ]
    }
  ];

  const progress = ((currentQuestion + 1) / questions.length) * 100;

  const handleAnswer = (value: string) => {
    setAnswers(prev => ({
      ...prev,
      [currentQuestion]: value
    }));
  };

  const handleNext = () => {
    if (currentQuestion < questions.length - 1) {
      setCurrentQuestion(prev => prev + 1);
    } else {
      setShowResults(true);
    }
  };

  const handlePrevious = () => {
    if (currentQuestion > 0) {
      setCurrentQuestion(prev => prev - 1);
    }
  };

  const calculateResults = () => {
    const results = {
      tech: 0,
      creative: 0,
      social: 0,
      business: 0
    };

    Object.values(answers).forEach(answer => {
      if (['tech', 'logical', 'independent', 'innovation'].includes(answer)) {
        results.tech++;
      } else if (['creative', 'spatial', 'flexible', 'impact'].includes(answer)) {
        results.creative++;
      } else if (['people', 'interpersonal', 'collaborative', 'growth'].includes(answer)) {
        results.social++;
      } else if (['business', 'linguistic', 'structured', 'stability'].includes(answer)) {
        results.business++;
      }
    });

    const maxScore = Math.max(...Object.values(results));
    const topArea = Object.keys(results).find(key => results[key as keyof typeof results] === maxScore);

    const areaMapping = {
      tech: { name: "Tecnologia & Inovação", color: "text-blue-600", careers: ["Engenharia de Software", "Cientista de Dados", "Desenvolvedor Mobile"] },
      creative: { name: "Criatividade & Design", color: "text-purple-600", careers: ["UX/UI Designer", "Designer Gráfico", "Arquiteto"] },
      social: { name: "Humanas & Sociais", color: "text-green-600", careers: ["Psicólogo", "Professor", "Assistente Social"] },
      business: { name: "Negócios & Gestão", color: "text-orange-600", careers: ["Administrador", "Consultor", "Analista Financeiro"] }
    };

    return {
      topArea: areaMapping[topArea as keyof typeof areaMapping] || areaMapping.tech,
      scores: results,
      confidence: Math.round((maxScore / questions.length) * 100)
    };
  };

  const results = showResults ? calculateResults() : null;

  if (showResults && results) {
    return (
      <div className="container mx-auto px-4 py-8">
        <div className="max-w-4xl mx-auto space-y-8">
          <div className="text-center space-y-4">
            <h1 className="text-4xl font-bold">
              Seus Resultados de
              <span className="text-gradient block">Orientação Vocacional</span>
            </h1>
            <p className="text-xl text-slate-600">
              Baseado nas suas respostas, aqui estão suas recomendações personalizadas
            </p>
          </div>

          <Card className="card-gradient text-center">
            <CardHeader>
              <CardTitle className="text-2xl">
                Sua Área Principal: 
                <span className={`${results.topArea.color} block text-3xl mt-2`}>
                  {results.topArea.name}
                </span>
              </CardTitle>
              <CardDescription>
                <Badge className="bg-gradient-vocia text-white text-lg px-4 py-2">
                  {results.confidence}% de Compatibilidade
                </Badge>
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div className="space-y-6">
                <div className="text-lg text-slate-600">
                  Com base no seu perfil, você tem grande afinidade com esta área!
                </div>
                
                <div className="grid md:grid-cols-3 gap-4">
                  {results.topArea.careers.map((career, index) => (
                    <Card key={index} className="bg-white/60">
                      <CardContent className="p-4 text-center">
                        <div className="font-semibold">{career}</div>
                      </CardContent>
                    </Card>
                  ))}
                </div>
              </div>
            </CardContent>
          </Card>

          <div className="grid md:grid-cols-2 gap-8">
            <Card className="card-gradient">
              <CardHeader>
                <CardTitle>Distribuição de Interesses</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                {Object.entries(results.scores).map(([area, score]) => {
                  const areaNames = {
                    tech: "Tecnologia",
                    creative: "Criatividade", 
                    social: "Social",
                    business: "Negócios"
                  };
                  const percentage = (score / questions.length) * 100;
                  
                  return (
                    <div key={area} className="space-y-2">
                      <div className="flex justify-between text-sm">
                        <span>{areaNames[area as keyof typeof areaNames]}</span>
                        <span>{Math.round(percentage)}%</span>
                      </div>
                      <Progress value={percentage} className="h-2" />
                    </div>
                  );
                })}
              </CardContent>
            </Card>

            <Card className="card-gradient">
              <CardHeader>
                <CardTitle>Próximos Passos</CardTitle>
              </CardHeader>
              <CardContent className="space-y-4">
                <div className="space-y-3">
                  <div className="flex items-center space-x-3">
                    <div className="w-2 h-2 bg-vocia-500 rounded-full"></div>
                    <span>Explore as carreiras recomendadas em detalhes</span>
                  </div>
                  <div className="flex items-center space-x-3">
                    <div className="w-2 h-2 bg-vocia-500 rounded-full"></div>
                    <span>Faça mais testes para refinar seus resultados</span>
                  </div>
                  <div className="flex items-center space-x-3">
                    <div className="w-2 h-2 bg-vocia-500 rounded-full"></div>
                    <span>Converse com um orientador especializado</span>
                  </div>
                  <div className="flex items-center space-x-3">
                    <div className="w-2 h-2 bg-vocia-500 rounded-full"></div>
                    <span>Pesquise cursos e instituições de ensino</span>
                  </div>
                </div>
                
                <Button onClick={onComplete} className="w-full btn-gradient mt-6">
                  Ir para Dashboard
                </Button>
              </CardContent>
            </Card>
          </div>
        </div>
      </div>
    );
  }

  return (
    <div className="container mx-auto px-4 py-8">
      <div className="max-w-2xl mx-auto space-y-8">
        <div className="text-center space-y-4">
          <h1 className="text-4xl font-bold">
            Avaliação de
            <span className="text-gradient block">Orientação Vocacional</span>
          </h1>
          <p className="text-xl text-slate-600">
            Responda as perguntas a seguir para descobrir seu perfil profissional
          </p>
        </div>

        <Card className="card-gradient">
          <CardHeader>
            <div className="flex items-center justify-between">
              <Badge variant="outline">
                {questions[currentQuestion].category}
              </Badge>
              <Badge>
                Pergunta {currentQuestion + 1} de {questions.length}
              </Badge>
            </div>
            <Progress value={progress} className="mt-4" />
          </CardHeader>
        </Card>

        <Card className="card-gradient">
          <CardHeader>
            <CardTitle className="text-xl">
              {questions[currentQuestion].question}
            </CardTitle>
          </CardHeader>
          <CardContent>
            <RadioGroup
              value={answers[currentQuestion] || ""}
              onValueChange={handleAnswer}
              className="space-y-4"
            >
              {questions[currentQuestion].options.map((option, index) => (
                <div key={index} className="flex items-center space-x-3 p-4 rounded-lg hover:bg-white/60 transition-colors">
                  <RadioGroupItem value={option.value} id={`option-${index}`} />
                  <Label htmlFor={`option-${index}`} className="flex-1 cursor-pointer">
                    {option.label}
                  </Label>
                </div>
              ))}
            </RadioGroup>
          </CardContent>
        </Card>

        <div className="flex justify-between">
          <Button
            variant="outline"
            onClick={handlePrevious}
            disabled={currentQuestion === 0}
          >
            ← Anterior
          </Button>
          
          <Button
            onClick={handleNext}
            disabled={!answers[currentQuestion]}
            className="btn-gradient"
          >
            {currentQuestion === questions.length - 1 ? 'Ver Resultados' : 'Próxima →'}
          </Button>
        </div>
      </div>
    </div>
  );
};

export default Assessment;

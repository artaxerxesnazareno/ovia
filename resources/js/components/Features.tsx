
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";

const Features = () => {
  const features = [
    {
      title: "Testes Vocacionais Avançados",
      description: "Questionários científicos que analisam suas preferências, habilidades e personalidade",
      icon: "🎯",
      items: ["Teste de Interesses Profissionais", "Análise de Inteligências Múltiplas", "Perfil Comportamental"]
    },
    {
      title: "Inteligência Artificial",
      description: "Algoritmos avançados que processam seus dados para recomendações precisas",
      icon: "🤖",
      items: ["Análise Preditiva", "Machine Learning", "Recomendações Personalizadas"]
    },
    {
      title: "Autoconhecimento",
      description: "Atividades interativas para você se conhecer melhor e descobrir seus valores",
      icon: "🧭",
      items: ["Agenda Colorida", "Trajetória de Vida", "Critérios de Escolha"]
    },
    {
      title: "Exploração de Carreiras",
      description: "Base de dados completa com informações detalhadas sobre profissões",
      icon: "🚀",
      items: ["500+ Profissões", "Tendências do Mercado", "Salários e Perspectivas"]
    },
    {
      title: "Orientação Especializada",
      description: "Acesso a psicólogos e orientadores vocacionais certificados",
      icon: "👨‍🎓",
      items: ["Sessões Online", "Planos Personalizados", "Acompanhamento Contínuo"]
    },
    {
      title: "Dashboard Inteligente",
      description: "Acompanhe seu progresso e visualize insights sobre sua jornada",
      icon: "📊",
      items: ["Gráficos Interativos", "Relatórios Detalhados", "Histórico Completo"]
    }
  ];

  return (
    <section className="py-20 bg-white/50">
      <div className="container mx-auto px-4">
        <div className="text-center mb-16 space-y-4">
          <h2 className="text-4xl font-bold">
            Como funciona nossa
            <span className="text-gradient block">plataforma inteligente</span>
          </h2>
          <p className="text-xl text-slate-600 max-w-3xl mx-auto">
            Combinamos ciência, tecnologia e experiência humana para oferecer 
            a orientação vocacional mais completa e precisa do mercado.
          </p>
        </div>

        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
          {features.map((feature, index) => (
            <Card 
              key={index} 
              className="card-gradient hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2 border-0"
            >
              <CardHeader className="text-center pb-4">
                <div className="text-4xl mb-4">{feature.icon}</div>
                <CardTitle className="text-xl font-bold text-slate-800">
                  {feature.title}
                </CardTitle>
                <CardDescription className="text-slate-600">
                  {feature.description}
                </CardDescription>
              </CardHeader>
              <CardContent>
                <ul className="space-y-2">
                  {feature.items.map((item, itemIndex) => (
                    <li key={itemIndex} className="flex items-center space-x-2">
                      <div className="w-2 h-2 bg-vocia-500 rounded-full"></div>
                      <span className="text-sm text-slate-700">{item}</span>
                    </li>
                  ))}
                </ul>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    </section>
  );
};

export default Features;

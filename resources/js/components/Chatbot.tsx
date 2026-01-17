
import { useState } from 'react';
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";

const Chatbot = () => {
  const [isOpen, setIsOpen] = useState(false);
  const [messages, setMessages] = useState([
    {
      id: 1,
      text: "Olá! Sou a OVIA, sua assistente de orientação vocacional. Como posso ajudá-lo hoje?",
      sender: "bot",
      timestamp: new Date()
    }
  ]);
  const [inputValue, setInputValue] = useState('');

  const quickReplies = [
    "Quero fazer um teste vocacional",
    "Como funciona a plataforma?",
    "Preciso de ajuda para escolher carreira",
    "Quais são as áreas mais promissoras?"
  ];

  const botResponses = {
    default: "Entendo sua pergunta! Nossa IA está processando a melhor resposta para você. Que tal começar com um de nossos testes vocacionais?",
    teste: "Excelente! Temos vários testes disponíveis: Teste de Interesses Profissionais, Análise de Inteligências Múltiplas e Perfil Comportamental. Por onde gostaria de começar?",
    plataforma: "Nossa plataforma combina testes científicos com IA para oferecer recomendações personalizadas de carreira. O processo é simples: você faz as avaliações, nossa IA analisa os resultados e você recebe sugestões detalhadas!",
    carreira: "Escolher uma carreira é uma decisão importante! Nossos testes vão ajudar você a identificar suas aptidões, interesses e valores. Também oferecemos orientação com especialistas. Quer começar?",
    areas: "As áreas mais promissoras incluem Tecnologia, Saúde Digital, Sustentabilidade, IA/Machine Learning e Biotecnologia. Mas o mais importante é encontrar algo que combine com seu perfil!"
  };

  const handleSendMessage = (text: string) => {
    if (!text.trim()) return;

    const userMessage = {
      id: messages.length + 1,
      text,
      sender: "user" as const,
      timestamp: new Date()
    };

    setMessages(prev => [...prev, userMessage]);
    setInputValue('');

    // Simular resposta da IA
    setTimeout(() => {
      let response = botResponses.default;

      if (text.toLowerCase().includes('teste')) response = botResponses.teste;
      else if (text.toLowerCase().includes('plataforma') || text.toLowerCase().includes('funciona')) response = botResponses.plataforma;
      else if (text.toLowerCase().includes('carreira') || text.toLowerCase().includes('escolh')) response = botResponses.carreira;
      else if (text.toLowerCase().includes('área') || text.toLowerCase().includes('promissor')) response = botResponses.areas;

      const botMessage = {
        id: messages.length + 2,
        text: response,
        sender: "bot" as const,
        timestamp: new Date()
      };

      setMessages(prev => [...prev, botMessage]);
    }, 1000);
  };

  return (
    <>
      {/* Botão do Chatbot */}
      <Button
        onClick={() => setIsOpen(!isOpen)}
        className="fixed bottom-6 right-6 w-16 h-16 rounded-full btn-gradient shadow-lg z-50 hover:scale-110 transition-transform"
      >
        {isOpen ? '✕' : '💬'}
      </Button>

      {/* Janela do Chatbot */}
      {isOpen && (
        <Card className="fixed bottom-24 right-6 w-96 h-96 shadow-2xl z-40 border-0 card-gradient">
          <CardHeader className="bg-gradient-vocia text-white rounded-t-lg">
            <CardTitle className="flex items-center space-x-2">
              <span>🤖</span>
              <span>VocIA Assistant</span>
              <Badge className="bg-white/20 text-white ml-auto">Online</Badge>
            </CardTitle>
          </CardHeader>

          <CardContent className="flex flex-col h-full p-0">
            {/* Messages */}
            <div className="flex-1 overflow-y-auto p-4 space-y-4 max-h-64">
              {messages.map((message) => (
                <div
                  key={message.id}
                  className={`flex ${message.sender === 'user' ? 'justify-end' : 'justify-start'}`}
                >
                  <div
                    className={`max-w-xs p-3 rounded-lg ${
                      message.sender === 'user'
                        ? 'bg-vocia-500 text-white'
                        : 'bg-white border shadow-sm'
                    }`}
                  >
                    <p className="text-sm">{message.text}</p>
                  </div>
                </div>
              ))}
            </div>

            {/* Quick Replies */}
            <div className="p-4 border-t bg-slate-50">
              <div className="flex flex-wrap gap-2 mb-3">
                {quickReplies.map((reply, index) => (
                  <Button
                    key={index}
                    variant="outline"
                    size="sm"
                    onClick={() => handleSendMessage(reply)}
                    className="text-xs hover:bg-vocia-50 hover:border-vocia-200"
                  >
                    {reply}
                  </Button>
                ))}
              </div>

              {/* Input */}
              <div className="flex space-x-2">
                <Input
                  value={inputValue}
                  onChange={(e) => setInputValue(e.target.value)}
                  placeholder="Digite sua pergunta..."
                  onKeyPress={(e) => e.key === 'Enter' && handleSendMessage(inputValue)}
                  className="flex-1"
                />
                <Button
                  onClick={() => handleSendMessage(inputValue)}
                  className="bg-vocia-500 hover:bg-vocia-600"
                >
                  ↑
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>
      )}
    </>
  );
};

export default Chatbot;

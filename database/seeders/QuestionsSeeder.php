<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class QuestionsSeeder extends Seeder
{
    public function run(): void
    {
        $questions = array_merge(
            $this->getInterestsQuestions(),
            $this->getSkillsQuestions(),
            $this->getValuesQuestions(),
            $this->getPersonalityQuestions(),
            $this->getOpenQuestions()
        );

        DB::table('questions')->insert($questions);
    }

    private function getInterestsQuestions(): array
    {
        return array_merge(
            $this->getKnowledgeAreasQuestions(),
            $this->getActivitiesQuestions(),
            $this->getWorkEnvironmentQuestions()
        );
    }

    private function getKnowledgeAreasQuestions(): array
    {
        return [
            [
                'category' => 'interests',
                'dimension' => 'areas_conhecimento',
                'question_text' => 'Tenho facilidade e interesse em resolver problemas matemáticos complexos',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.2,
                'order' => 1,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'areas_conhecimento',
                'question_text' => 'Gosto de trabalhar com tecnologia, computadores e sistemas digitais',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 2,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'areas_conhecimento',
                'question_text' => 'Interesso-me por entender como as coisas funcionam e desmontar/aparelhos para ver seu interior',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 3,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'areas_conhecimento',
                'question_text' => 'Gosto de atividades que envolvem criatividade, arte e expressão pessoal',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 4,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getActivitiesQuestions(): array
    {
        return [
            [
                'category' => 'interests',
                'dimension' => 'atividades',
                'question_text' => 'Prefiro trabalhar com:',
                'question_type' => 'multiple',
                'options' => json_encode([
                    'Números e dados',
                    'Pessoas e relacionamentos',
                    'Criação e design',
                    'Tecnologia e sistemas'
                ]),
                'weight' => 1.3,
                'order' => 5,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'atividades',
                'question_text' => 'Gosto de atividades que envolvem análise e interpretação de informações',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 6,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'atividades',
                'question_text' => 'Prefiro tarefas que envolvem planejamento e organização detalhada',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 7,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getWorkEnvironmentQuestions(): array
    {
        return [
            [
                'category' => 'interests',
                'dimension' => 'ambientes',
                'question_text' => 'Prefiro trabalhar em ambiente:',
                'question_type' => 'multiple',
                'options' => json_encode([
                    'Escritório com equipamentos tecnológicos',
                    'Laboratório ou oficina técnica',
                    'Ar livre ou em obras',
                    'Ambiente hospitalar ou clínico',
                    'Sala de aula ou ambiente educacional'
                ]),
                'weight' => 1.2,
                'order' => 8,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'ambientes',
                'question_text' => 'Gosto de trabalhar em equipe e colaborar com outras pessoas',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 9,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'ambientes',
                'question_text' => 'Prefiro ambientes de trabalho dinâmicos e com novos desafios constantes',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 10,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'ambientes',
                'question_text' => 'Valorizo ambientes de trabalho estruturados e com rotinas definidas',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 11,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'ambientes',
                'question_text' => 'Gosto de trabalhar com prazos definidos e metas claras',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 12,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getSkillsQuestions(): array
    {
        return array_merge(
            $this->getLogicalReasoningQuestions(),
            $this->getCommunicationQuestions(),
            $this->getCreativityQuestions(),
            $this->getTeamworkQuestions(),
            $this->getLeadershipQuestions()
        );
    }

    private function getLogicalReasoningQuestions(): array
    {
        return [
            [
                'category' => 'skills',
                'dimension' => 'raciocinio_logico',
                'question_text' => 'Consigo resolver problemas lógicos e quebra-cabeças com facilidade',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.3,
                'order' => 1,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'skills',
                'dimension' => 'raciocinio_logico',
                'question_text' => 'Tenho facilidade em aprender novas tecnologias e softwares',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.2,
                'order' => 2,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getCommunicationQuestions(): array
    {
        return [
            [
                'category' => 'skills',
                'dimension' => 'comunicacao',
                'question_text' => 'Tenho facilidade em comunicar ideias complexas de forma clara',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 3,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'skills',
                'dimension' => 'comunicacao',
                'question_text' => 'Sou bom em escrever textos e relatórios',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 4,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'skills',
                'dimension' => 'comunicacao',
                'question_text' => 'Consigo negociar e persuadir pessoas facilmente',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 5,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getCreativityQuestions(): array
    {
        return [
            [
                'category' => 'skills',
                'dimension' => 'criatividade',
                'question_text' => 'Sou criativo e costumo ter ideias originais e inovadoras',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.2,
                'order' => 6,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'skills',
                'dimension' => 'criatividade',
                'question_text' => 'Tenho facilidade para desenhar, pintar ou expressar-me artisticamente',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 7,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getTeamworkQuestions(): array
    {
        return [
            [
                'category' => 'skills',
                'dimension' => 'trabalho_equipe',
                'question_text' => 'Trabalho bem em equipe e consigo colaborar efetivamente com colegas',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 8,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getLeadershipQuestions(): array
    {
        return [
            [
                'category' => 'skills',
                'dimension' => 'lideranca',
                'question_text' => 'Tenho facilidade em liderar grupos e coordenar atividades',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 9,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'skills',
                'dimension' => 'lideranca',
                'question_text' => 'Consigo tomar decisões difíceis sob pressão',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 10,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getValuesQuestions(): array
    {
        return array_merge(
            $this->getPurposeQuestions(),
            $this->getWorkLifeBalanceQuestions(),
            $this->getRemunerationQuestions(),
            $this->getSocialImpactQuestions(),
            $this->getStabilityInnovationQuestions()
        );
    }

    private function getPurposeQuestions(): array
    {
        return [
            [
                'category' => 'values',
                'dimension' => 'proposito',
                'question_text' => 'Valorizo ter um trabalho que faz diferença na sociedade',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.2,
                'order' => 1,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'values',
                'dimension' => 'proposito',
                'question_text' => 'Para mim, é importante trabalhar com algo que tenha significado pessoal',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 2,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getWorkLifeBalanceQuestions(): array
    {
        return [
            [
                'category' => 'values',
                'dimension' => 'equilibrio',
                'question_text' => 'Prefiro um trabalho que permita equilíbrio entre vida pessoal e profissional',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 3,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'values',
                'dimension' => 'equilibrio',
                'question_text' => 'Estou disposto(a) a trabalhar muitas horas se for necessário para o sucesso profissional',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 4,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getRemunerationQuestions(): array
    {
        return [
            [
                'category' => 'values',
                'dimension' => 'remuneracao',
                'question_text' => 'Ter um salário alto é uma das minhas principais prioridades na carreira',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.2,
                'order' => 5,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getSocialImpactQuestions(): array
    {
        return [
            [
                'category' => 'values',
                'dimension' => 'impacto',
                'question_text' => 'É importante para mim que meu trabalho tenha impacto positivo na comunidade',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 6,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getStabilityInnovationQuestions(): array
    {
        return [
            [
                'category' => 'values',
                'dimension' => 'estabilidade_inovacao',
                'question_text' => 'Prefiro estabilidade e segurança no emprego a assumir riscos profissionais',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 7,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'values',
                'dimension' => 'estabilidade_inovacao',
                'question_text' => 'Gosto de desafios e de trabalhar em áreas inovadoras e de vanguarda',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 8,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getPersonalityQuestions(): array
    {
        return array_merge(
            $this->getIntroversionExtraversionQuestions(),
            $this->getAnalyticalCreativeQuestions(),
            $this->getPracticalTheoreticalQuestions(),
            $this->getDetailedVisionaryQuestions()
        );
    }

    private function getIntroversionExtraversionQuestions(): array
    {
        return [
            [
                'category' => 'personality',
                'dimension' => 'introversao_extroversao',
                'question_text' => 'Prefiro trabalhar sozinho(a) do que em grupo',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 1,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'personality',
                'dimension' => 'introversao_extroversao',
                'question_text' => 'Sou uma pessoa sociável e gosto de interagir com muitas pessoas',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 2,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getAnalyticalCreativeQuestions(): array
    {
        return [
            [
                'category' => 'personality',
                'dimension' => 'analitico_criativo',
                'question_text' => 'Sou mais analítico(a) e lógico(a) do que criativo(a) e intuitivo(a)',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.2,
                'order' => 3,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'personality',
                'dimension' => 'analitico_criativo',
                'question_text' => 'Costumo pensar "fora da caixa" e buscar soluções não convencionais',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 4,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getPracticalTheoreticalQuestions(): array
    {
        return [
            [
                'category' => 'personality',
                'dimension' => 'pratico_teorico',
                'question_text' => 'Sou mais prático(a) do que teórico(a) - prefiro "fazer" a "estudar sobre"',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.1,
                'order' => 5,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'personality',
                'dimension' => 'pratico_teorico',
                'question_text' => 'Gosto de estudar teorias e conceitos antes de colocar em prática',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 6,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getDetailedVisionaryQuestions(): array
    {
        return [
            [
                'category' => 'personality',
                'dimension' => 'detalhista_visionario',
                'question_text' => 'Sou detalhista e presto atenção a pequenos pormenores',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 7,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'personality',
                'dimension' => 'detalhista_visionario',
                'question_text' => 'Sou mais visionário(a) e foco no "quadro geral" do que nos detalhes',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 8,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'personality',
                'dimension' => 'detalhista_visionario',
                'question_text' => 'Sou organizado(a) e metódico(a) no meu trabalho',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 9,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'personality',
                'dimension' => 'detalhista_visionario',
                'question_text' => 'Adapto-me facilmente a mudanças e situações inesperadas',
                'question_type' => 'likert',
                'options' => null,
                'weight' => 1.0,
                'order' => 10,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getOpenQuestions(): array
    {
        return [
            [
                'category' => 'interests',
                'dimension' => 'atividades_absorcao',
                'question_text' => 'Descreva uma atividade que você realiza e sente-se completamente absorvido (perde a noção do tempo):',
                'question_type' => 'open',
                'options' => null,
                'weight' => 1.5,
                'order' => 1,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'admiracoes_profissionais',
                'question_text' => 'Qual profissional você admira e por quê? (Pode ser alguém conhecido ou uma profissão em geral)',
                'question_type' => 'open',
                'options' => null,
                'weight' => 1.3,
                'order' => 2,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'category' => 'interests',
                'dimension' => 'visao_futuro',
                'question_text' => 'Como você se vê daqui a 10 anos? Descreva sua vida profissional e pessoal ideal:',
                'question_type' => 'open',
                'options' => null,
                'weight' => 1.4,
                'order' => 3,
                'is_required' => true,
                'is_active' => true,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }
}

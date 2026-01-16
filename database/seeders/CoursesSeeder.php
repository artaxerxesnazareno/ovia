<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CoursesSeeder extends Seeder
{
    const HORAS = '60 horas';

    public function run(): void
    {
        // Opção 1: Limpar e inserir (recomendado para seeders)
        DB::statement('PRAGMA foreign_keys = OFF;'); // Para SQLite
        DB::table('career_paths')->truncate();
        DB::table('courses')->truncate();
        DB::statement('PRAGMA foreign_keys = ON;');

        DB::table('courses')->insert($this->getCoursesData());
        $this->createCareerPaths();
    }

    private function getCoursesData(): array
    {
        return [
            $this->getComputerScienceCourse(),
            $this->getElectronicsTelecomCourse(),
            $this->getArchitectureCourse(),
            $this->getComputerEngineeringCourse(),
            $this->getHumanResourcesCourse(),
            $this->getAccountingCourse(),
            $this->getLawCourse(),
            $this->getNursingCourse(),
            $this->getPsychologyCourse(),
            $this->getMarketingCourse(),
        ];
    }

    private function getComputerScienceCourse(): array
    {
        return [
            'name' => 'Licenciatura em Ciências da Computação',
            'slug' => 'ciencias-da-computacao',
            'description' => 'Curso concebido para formar licenciados com conhecimento sólido dos problemas relacionados com a utilização dos métodos e técnicas computacionais. Forma profissionais capazes de desenvolver métodos, técnicas e ferramentas computacionais próprias.',
            'duration_semesters' => 8,
            'shifts' => json_encode(['morning', 'evening']),
            'vacancies_per_year' => 40,
            'coordinator_name' => 'Dr. Miguel dos Santos',
            'curriculum' => $this->getComputerScienceCurriculum(),
            'admission_requirements' => 'Certificado do Ensino Médio, Prova de aptidão em matemática, Entrevista de seleção',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function getElectronicsTelecomCourse(): array
    {
        return [
            'name' => 'Licenciatura em Electrónica e Telecomunicações',
            'slug' => 'electronica-telecomunicacoes',
            'description' => 'Formar engenheiros nas áreas de Electrotecnia, Electrónica e Telecomunicações para actuarem nos domínios de sistemas de energia, sistemas de automatização, produção e electrónica industrial, sistemas de Telecomunicações.',
            'duration_semesters' => 8,
            'shifts' => json_encode(['morning', 'evening']),
            'vacancies_per_year' => 35,
            'coordinator_name' => 'Eng. Carlos Mendes',
            'curriculum' => $this->getElectronicsTelecomCurriculum(),
            'admission_requirements' => 'Certificado do Ensino Médio, Conhecimentos básicos de física e matemática, Teste de aptidão técnica',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function getArchitectureCourse(): array
    {
        return [
            'name' => 'Licenciatura em Arquitetura',
            'slug' => 'arquitetura',
            'description' => 'Formação de arquitetos para organizar espaços internos, criar identidades visuais, desenvolver espaços abertos, projetar e coordenar obras, realizar iluminação e recuperar património histórico.',
            'duration_semesters' => 8,
            'shifts' => json_encode(['morning']),
            'vacancies_per_year' => 30,
            'coordinator_name' => 'Arq. Ana Pereira',
            'curriculum' => $this->getArchitectureCurriculum(),
            'admission_requirements' => 'Certificado do Ensino Médio, Prova de aptidão em desenho, Portfolio de trabalhos artísticos',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function getComputerEngineeringCourse(): array
    {
        return [
            'name' => 'Licenciatura em Engenharia Informática',
            'slug' => 'engenharia-informatica',
            'description' => 'Formação de engenheiros informáticos especializados em desenvolvimento de software, gestão de projetos tecnológicos, arquitetura de sistemas e inovação tecnológica.',
            'duration_semesters' => 8,
            'shifts' => json_encode(['morning', 'evening']),
            'vacancies_per_year' => 45,
            'coordinator_name' => 'Eng. Pedro Silva',
            'curriculum' => json_encode([
                'disciplinas' => [
                    'Programação Avançada',
                    'Engenharia de Software',
                    'Arquitetura de Sistemas',
                    'Gestão de Projetos de TI',
                    'Redes de Computadores',
                    'Inteligência Artificial',
                    'Segurança Informática'
                ]
            ]),
            'admission_requirements' => 'Certificado do Ensino Médio, Conhecimentos básicos de matemática e lógica',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function getHumanResourcesCourse(): array
    {
        return [
            'name' => 'Licenciatura em Gestão de Recursos Humanos',
            'slug' => 'gestao-recursos-humanos',
            'description' => 'Formação de gestores especializados em recrutamento, seleção, desenvolvimento de talentos, gestão de desempenho e relações laborais.',
            'duration_semesters' => 6,
            'shifts' => json_encode(['morning', 'evening', 'night']),
            'vacancies_per_year' => 50,
            'coordinator_name' => 'Dra. Marta Fernandes',
            'curriculum' => json_encode([
                'disciplinas' => [
                    'Gestão de Pessoas',
                    'Recrutamento e Seleção',
                    'Desenvolvimento Organizacional',
                    'Legislação Laboral',
                    'Psicologia Organizacional',
                    'Gestão da Formação'
                ]
            ]),
            'admission_requirements' => 'Certificado do Ensino Médio, Boas capacidades de comunicação',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function getAccountingCourse(): array
    {
        return [
            'name' => 'Licenciatura em Contabilidade e Auditoria',
            'slug' => 'contabilidade-auditoria',
            'description' => 'Formação de profissionais especializados em contabilidade, auditoria, fiscalidade e gestão financeira empresarial.',
            'duration_semesters' => 6,
            'shifts' => json_encode(['morning', 'evening']),
            'vacancies_per_year' => 40,
            'coordinator_name' => 'Dr. João Martins',
            'curriculum' => json_encode([
                'disciplinas' => [
                    'Contabilidade Geral',
                    'Contabilidade de Gestão',
                    'Auditoria Financeira',
                    'Fiscalidade',
                    'Análise Financeira',
                    'Sistemas de Informação Contabilística'
                ]
            ]),
            'admission_requirements' => 'Certificado do Ensino Médio, Aptidão para matemática e análise',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function getLawCourse(): array
    {
        return [
            'name' => 'Licenciatura em Direito',
            'slug' => 'direito',
            'description' => 'Formação jurídica abrangente preparando profissionais para atuação em diversas áreas do direito angolano e internacional.',
            'duration_semesters' => 8,
            'shifts' => json_encode(['morning', 'evening']),
            'vacancies_per_year' => 60,
            'coordinator_name' => 'Dr. António Costa',
            'curriculum' => json_encode([
                'disciplinas' => [
                    'Introdução ao Direito',
                    'Direito Civil',
                    'Direito Penal',
                    'Direito Comercial',
                    'Direito Constitucional',
                    'Direito Internacional'
                ]
            ]),
            'admission_requirements' => 'Certificado do Ensino Médio, Boa capacidade de leitura e interpretação',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function getNursingCourse(): array
    {
        return [
            'name' => 'Licenciatura em Enfermagem',
            'slug' => 'enfermagem',
            'description' => 'Formação de enfermeiros generalistas com competências para cuidados de saúde, prevenção de doenças e promoção da saúde.',
            'duration_semesters' => 8,
            'shifts' => json_encode(['morning']),
            'vacancies_per_year' => 35,
            'coordinator_name' => 'Enf. Maria Santos',
            'curriculum' => json_encode([
                'disciplinas' => [
                    'Anatomia Humana',
                    'Fisiologia',
                    'Enfermagem Fundamental',
                    'Saúde Pública',
                    'Farmácia',
                    'Práticas Clínicas'
                ]
            ]),
            'admission_requirements' => 'Certificado do Ensino Médio, Prova de aptidão biológica, Entrevista motivacional',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function getPsychologyCourse(): array
    {
        return [
            'name' => 'Licenciatura em Psicologia',
            'slug' => 'psicologia',
            'description' => 'Formação em psicologia com ênfase em áreas clínica, organizacional, educacional e social.',
            'duration_semesters' => 8,
            'shifts' => json_encode(['morning', 'evening']),
            'vacancies_per_year' => 40,
            'coordinator_name' => 'Dra. Sofia Almeida',
            'curriculum' => json_encode([
                'disciplinas' => [
                    'Introdução à Psicologia',
                    'Psicologia do Desenvolvimento',
                    'Psicologia Social',
                    'Psicologia Clínica',
                    'Psicometria',
                    'Psicopatologia'
                ]
            ]),
            'admission_requirements' => 'Certificado do Ensino Médio, Entrevista psicológica, Capacidade empática',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function getMarketingCourse(): array
    {
        return [
            'name' => 'Licenciatura em Marketing',
            'slug' => 'marketing',
            'description' => 'Formação de profissionais para atuação em marketing estratégico, comunicação digital, pesquisa de mercado e gestão de marcas.',
            'duration_semesters' => 6,
            'shifts' => json_encode(['morning', 'evening']),
            'vacancies_per_year' => 45,
            'coordinator_name' => 'Prof. Rui Gomes',
            'curriculum' => json_encode([
                'disciplinas' => [
                    'Fundamentos de Marketing',
                    'Comportamento do Consumidor',
                    'Marketing Digital',
                    'Pesquisa de Mercado',
                    'Gestão de Marcas',
                    'Comunicação Empresarial'
                ]
            ]),
            'admission_requirements' => 'Certificado do Ensino Médio, Criatividade, Capacidade analítica',
            'is_active' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }

    private function createCareerPaths(): void
    {
        $careerPaths = array_merge(
            $this->getComputerScienceCareers(),
            $this->getElectronicsTelecomCareers(),
            $this->getArchitectureCareers(),
            $this->getEngineeringCareers(),
            $this->getHumanResourcesCareers()
        // Adicione outros métodos de carreiras aqui conforme necessário
        );

        foreach ($careerPaths as $career) {
            DB::table('career_paths')->insert($career);
        }
    }

    private function getComputerScienceCareers(): array
    {
        return [
            [
                'course_id' => 1,
                'title' => 'Desenvolvedor Full-Stack',
                'description' => 'Profissional que trabalha tanto no front-end quanto no back-end de aplicações web e móveis.',
                'average_salary_min' => 150000,
                'average_salary_max' => 400000,
                'market_demand' => 'high',
                'key_skills' => json_encode(['JavaScript', 'Python', 'SQL', 'React', 'Node.js', 'APIs']),
                'growth_potential' => json_encode(['Emprego estável', 'Crescimento rápido', 'Opções de freelancing']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'course_id' => 1,
                'title' => 'Engenheiro de Dados',
                'description' => 'Especialista em manipulação, análise e gestão de grandes volumes de dados.',
                'average_salary_min' => 200000,
                'average_salary_max' => 500000,
                'market_demand' => 'very_high',
                'key_skills' => json_encode(['Python', 'SQL', 'Hadoop', 'Spark', 'Machine Learning']),
                'growth_potential' => json_encode(['Setor em expansão', 'Salários competitivos', 'Demanda crescente']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getElectronicsTelecomCareers(): array
    {
        return [
            [
                'course_id' => 2,
                'title' => 'Engenheiro de Telecomunicações',
                'description' => 'Profissional responsável por projetar, implementar e manter sistemas de telecomunicações.',
                'average_salary_min' => 180000,
                'average_salary_max' => 450000,
                'market_demand' => 'high',
                'key_skills' => json_encode(['Redes', 'Sistemas de transmissão', 'Protocolos de comunicação']),
                'growth_potential' => json_encode(['Expansão do 5G', 'Infraestrutura nacional', 'Tecnologias emergentes']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getArchitectureCareers(): array
    {
        return [
            [
                'course_id' => 3,
                'title' => 'Arquiteto Residencial',
                'description' => 'Especializado em projetar e supervisionar construções de residências e edifícios habitacionais.',
                'average_salary_min' => 120000,
                'average_salary_max' => 350000,
                'market_demand' => 'medium',
                'key_skills' => json_encode(['AutoCAD', 'SketchUp', 'Legislação urbana', 'Gestão de obras']),
                'growth_potential' => json_encode(['Setor imobiliário em crescimento', 'Reabilitação urbana']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getEngineeringCareers(): array
    {
        return [
            [
                'course_id' => 4,
                'title' => 'Arquiteto de Software',
                'description' => 'Profissional responsável pelo design e estrutura de sistemas de software complexos.',
                'average_salary_min' => 250000,
                'average_salary_max' => 600000,
                'market_demand' => 'very_high',
                'key_skills' => json_encode(['Design Patterns', 'Microservices', 'Cloud Computing', 'DevOps']),
                'growth_potential' => json_encode(['Alta demanda', 'Salários elevados', 'Posições de liderança']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getHumanResourcesCareers(): array
    {
        return [
            [
                'course_id' => 5,
                'title' => 'Gestor de Recursos Humanos',
                'description' => 'Responsável pelo recrutamento, seleção, desenvolvimento e gestão de pessoas nas organizações.',
                'average_salary_min' => 100000,
                'average_salary_max' => 300000,
                'market_demand' => 'medium',
                'key_skills' => json_encode(['Recrutamento', 'Gestão de desempenho', 'Legislação laboral', 'Comunicação']),
                'growth_potential' => json_encode(['Crescimento organizacional', 'Posições estratégicas']),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];
    }

    private function getComputerScienceCurriculum(): string
    {
        return json_encode([
            '1º Ano' => [
                '1º Semestre' => [
                    'Programação I' => self::HORAS,
                    'Comunicação Escrita' => self::HORAS,
                    'Inglês Técnico' => self::HORAS,
                    'Análise Matemática I' => self::HORAS,
                    'Metodologia de Investigação Científica' => self::HORAS,
                    'Introdução à Ciência da Computação' => self::HORAS,
                    'Lógica Matemática' => self::HORAS
                ],
                '2º Semestre' => [
                    'Programação II' => self::HORAS,
                    'Fundamentos de Sistemas de Informação' => self::HORAS,
                    'Física Computacional' => self::HORAS,
                    'Análise Matemática II' => self::HORAS,
                    'Álgebra Linear e Geometria Analítica' => self::HORAS,
                    'Sistemas Digitais e Computadores' => self::HORAS
                ]
            ]
        ]);
    }

    private function getElectronicsTelecomCurriculum(): string
    {
        return json_encode([
            '1º Ano' => [
                '1º Semestre' => [
                    'Língua Portuguesa I' => self::HORAS,
                    'Inglês Instrumental' => self::HORAS,
                    'Introdução à Computação' => self::HORAS,
                    'Introdução à Engenharia Electrónica e Telecomunicações' => self::HORAS,
                    'Análise Matemática I' => self::HORAS,
                    'Física I' => self::HORAS,
                    'Metodologia de Investigação Científica' => self::HORAS
                ],
                '2º Semestre' => [
                    'Introdução à Programação' => self::HORAS,
                    'Análise Matemática II' => self::HORAS,
                    'Física II' => self::HORAS,
                    'Química Fundamental' => self::HORAS,
                    'Álgebra Linear e Geometria Analítica' => self::HORAS,
                    'Introdução às Telecomunicações' => self::HORAS
                ]
            ]
        ]);
    }

    private function getArchitectureCurriculum(): string
    {
        return json_encode([
            '1º Ano' => [
                '1º Semestre' => [
                    'Língua Portuguesa' => self::HORAS,
                    'Desenho Técnico I' => self::HORAS,
                    'Introdução à Arquitetura I' => self::HORAS,
                    'Matemática para Arquitetura I' => self::HORAS,
                    'Física Geral' => self::HORAS,
                    'Informática para Arquitetura' => self::HORAS
                ],
                '2º Semestre' => [
                    'Introdução à Arquitetura' => self::HORAS,
                    'Desenho Técnico II' => self::HORAS,
                    'Desenho Plástico' => self::HORAS,
                    'Geometria Descritiva II' => self::HORAS,
                    'Mecânica Aplicada' => self::HORAS,
                    'Metodologia de Investigação Científica' => self::HORAS
                ]
            ]
        ]);
    }
}

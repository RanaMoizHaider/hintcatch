// HintCatch Data Models

export interface Agent {
    id: number;
    name: string;
    slug: string;
    description: string;
    logo: string | null;
    website: string | null;
    docs_url: string | null;
    github_url: string | null;
    supported_config_types: string[];
    supported_file_formats: string[];
    mcp_config_template: McpConfigTemplate | null;
    supports_mcp: boolean;
    mcp_transport_types: string[] | null;
    mcp_config_paths: McpConfigPaths | null;
    rules_filename: string | null;
    skills_config_template: SkillsConfigTemplate | null;
    config_type_templates: Record<string, ConfigTypeTemplate> | null;
    created_at: string;
    updated_at: string;
    // Counts
    configs_count?: number;
    skills_count?: number;
}

export interface SkillsConfigTemplate {
    config_format: string;
    global_path: string;
    project_path?: string;
    file_extension: string;
    supports_subdirectories?: boolean;
}

export interface ConfigTypeTemplate {
    global_path?: string;
    project_path?: string;
    config_format?: string;
    file_extension?: string;
    install_method?: string;
    install_command?: string;
    // OpenCode plugin-specific (npm install via config)
    npm_install?: {
        config_file: string;
        config_key: string;
        example: string;
    };
    // Claude Code plugin-specific (marketplace)
    marketplace_add_command?: string;
    scopes?: string[];
    plugin_structure?: {
        manifest: string;
        commands_dir: string;
        agents_dir: string;
        skills_dir: string;
        hooks_file: string;
        mcp_config: string;
        lsp_config: string;
    };
    test_command?: string;
}

export interface McpConfigPaths {
    project?: string;
    global?: string;
    local?: string;
}

export interface McpConfigTemplate {
    wrapper_key: string;
    config_format: string;
    [transportType: string]:
        | string
        | McpTransportConfig
        | Record<string, unknown>
        | undefined;
}

export interface McpTransportConfig {
    type_value?: string;
    fields: Record<string, string>;
    settings_wrapper?: string;
}

export interface ConfigType {
    id: number;
    name: string;
    slug: string;
    description: string;
    allowed_formats: string[];
    allows_multiple_files: boolean;
    is_standard: boolean;
    standard_url: string | null;
    requires_agent: boolean;
    created_at: string;
    updated_at: string;
    // Relations
    categories?: Category[];
    // Counts
    configs_count?: number;
}

export interface Category {
    id: number;
    config_type_id: number;
    name: string;
    slug: string;
    description: string;
    created_at: string;
    updated_at: string;
    // Relations
    config_type?: ConfigType;
    // Counts
    configs_count?: number;
}

export interface Config {
    id: number;
    submitted_by: number | null;
    agent_id: number | null;
    config_type_id: number;
    category_id: number | null;
    name: string;
    slug: string;
    description: string;
    source_url: string | null;
    source_author: string | null;
    github_url: string | null;
    instructions: string | null;
    readme: string | null;
    uses_standard_install: boolean;
    vote_score: number;
    is_featured: boolean;
    created_at: string;
    updated_at: string;
    // Relations
    submitter?: PublicUser;
    agent?: Agent;
    config_type?: ConfigType;
    category?: Category;
    files?: ConfigFile[];
    // Computed
    primary_file?: ConfigFile;
}

export interface ConfigFile {
    id: number;
    config_id: number;
    filename: string;
    path: string | null;
    content: string;
    language: string;
    is_primary: boolean;
    order: number;
    created_at: string;
    updated_at: string;
    // Computed
    full_path?: string;
}

export interface Skill {
    id: number;
    submitted_by: number | null;
    category_id: number | null;
    name: string;
    slug: string;
    description: string;
    content: string;
    license: string | null;
    compatibility: SkillCompatibility | null;
    metadata: Record<string, unknown> | null;
    allowed_tools: string[] | null;
    scripts: SkillScript[] | null;
    references: SkillReference[] | null;
    assets: string[] | null;
    source_url: string | null;
    source_author: string | null;
    readme: string | null;
    github_url: string | null;
    vote_score: number;
    is_featured: boolean;
    created_at: string;
    updated_at: string;
    // Relations
    submitter?: PublicUser;
    category?: Category;
}

export interface SkillCompatibility {
    agents?: string[];
    platforms?: string[];
    languages?: string[];
}

export interface SkillScript {
    name: string;
    content: string;
}

export interface SkillReference {
    name: string;
    content: string;
}

export interface SkillIntegration {
    agent: Agent;
    integration: {
        skill_md: string;
        install_path?: string | null;
        project_path?: string | null;
    };
}

export interface McpServer {
    id: number;
    submitted_by: number | null;
    name: string;
    slug: string;
    description: string;
    type: 'remote' | 'local';
    url: string | null;
    command: string | null;
    args: string[] | null;
    env: Record<string, string> | null;
    headers: Record<string, string> | null;
    source_url: string | null;
    source_author: string | null;
    readme: string | null;
    github_url: string | null;
    vote_score: number;
    is_featured: boolean;
    created_at: string;
    updated_at: string;
    // Relations
    submitter?: PublicUser;
}

export interface Prompt {
    id: number;
    submitted_by: number | null;
    name: string;
    slug: string;
    description: string;
    content: string;
    category:
        | 'system'
        | 'task'
        | 'review'
        | 'documentation'
        | 'debugging'
        | 'refactoring';
    source_url: string | null;
    source_author: string | null;
    github_url: string | null;
    vote_score: number;
    is_featured: boolean;
    created_at: string;
    updated_at: string;
    // Relations
    submitter?: PublicUser;
}

export interface PublicUser {
    id: number;
    name: string;
    username: string;
    avatar: string | null;
    bio: string | null;
    website: string | null;
    github_username: string | null;
    created_at: string;
    // Counts
    configs_count?: number;
    prompts_count?: number;
    mcp_servers_count?: number;
    skills_count?: number;
}

export interface Comment {
    id: number;
    user_id: number;
    parent_id: number | null;
    body: string;
    is_edited: boolean;
    edited_at: string | null;
    created_at: string;
    updated_at: string;
    vote_score: number;
    user_vote: 1 | -1 | null;
    user?: PublicUser;
    replies?: Comment[];
}

export interface Vote {
    id: number;
    user_id: number;
    value: 1 | -1;
    created_at: string;
}

export interface Favorite {
    id: number;
    user_id: number;
    favoritable_type: string;
    favoritable_id: number;
    created_at: string;
}

export interface InteractionStatus {
    user_vote: 1 | -1 | null;
    is_favorited: boolean;
    favorites_count: number;
}

// Page Props Types
export interface HomePageProps {
    recentConfigs: Config[];
    recentMcpServers: McpServer[];
    recentPrompts: Prompt[];
    recentSkills: Skill[];
    topConfigs: Config[];
    topMcpServers: McpServer[];
    topPrompts: Prompt[];
    topSkills: Skill[];
    agents: Agent[];
    configTypes: ConfigType[];
    stats: {
        totalConfigs: number;
        totalMcpServers: number;
        totalPrompts: number;
        totalSkills: number;
        totalUsers: number;
    };
}

export interface AgentIndexPageProps {
    agents: Agent[];
}

export interface ConfigsByTypeEntry {
    configType: ConfigType;
    recent: Config[];
    top: Config[];
}

export interface AgentShowPageProps {
    agent: Agent;
    configTypes: ConfigType[];
    configsByType: Record<string, ConfigsByTypeEntry>;
    mcpServerCount?: number;
    skillsCount?: number;
}

export interface AgentConfigsPageProps {
    agent: Agent;
    configType: ConfigType;
    configs: PaginatedData<Config>;
    categories: Category[];
    filters: {
        sort: 'recent' | 'top';
        category: string | null;
    };
    totalCount: number;
}

export interface ConfigTypeIndexPageProps {
    configTypes: ConfigType[];
}

export interface ConfigTypeShowPageProps {
    configType: ConfigType;
    configs: Config[];
    categories: Category[];
    agents: Agent[];
}

export interface ConfigShowPageProps {
    config: Config;
    pluginInstallTemplate: ConfigTypeTemplate | null;
    relatedConfigs: Config[];
    moreFromUser: Config[];
    comments: Comment[];
    interaction: InteractionStatus;
}

export interface McpServerIndexPageProps {
    mcpServers: McpServer[];
    featuredMcpServers: McpServer[];
}

export interface AgentIntegration {
    agent: Agent;
    integration: {
        json_config: Record<string, unknown>;
        config_paths: McpConfigPaths;
        cli_command: string | null;
        transport_type: string;
    };
}

export interface McpServerShowPageProps {
    mcpServer: McpServer;
    agentIntegrations: Record<string, AgentIntegration>;
    moreFromUser: McpServer[];
    comments: Comment[];
    interaction: InteractionStatus;
}

export interface SkillIndexPageProps {
    skills: Skill[];
    featuredSkills: Skill[];
}

export interface SkillShowPageProps {
    skill: Skill;
    agentIntegrations: Record<string, SkillIntegration>;
    moreFromUser: Skill[];
    comments: Comment[];
    interaction: InteractionStatus;
}

export interface PaginatedData<T> {
    data: T[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        from: number | null;
        last_page: number;
        path: string;
        per_page: number;
        to: number | null;
        total: number;
    };
}

export interface PromptIndexPageProps {
    prompts: PaginatedData<Prompt>;
    featuredPrompts: Prompt[];
    categories: string[];
}

export interface PromptShowPageProps {
    prompt: Prompt;
    relatedPrompts: Prompt[];
    moreFromUser: Prompt[];
    comments: Comment[];
    interaction: InteractionStatus;
}

export interface UserProfilePageProps {
    profileUser: PublicUser;
    configs: Config[];
    prompts: Prompt[];
    mcpServers: McpServer[];
    skills: Skill[];
    stats: {
        totalConfigs: number;
        totalMcpServers: number;
        totalPrompts: number;
        totalSkills: number;
        totalVotes: number;
    };
}

// Submit Page Props
export interface SubmitIndexPageProps {
    agents: Agent[];
    configTypes: ConfigType[];
}

export interface SubmitConfigPageProps {
    agents: Agent[];
    configTypes: ConfigType[];
}

export interface SubmitMcpServerPageProps {
    agents: Agent[];
}

export interface SubmitSkillPageProps {
    agents: Agent[];
    categories: Category[];
}

export interface SubmitPromptPageProps {
    // No specific props needed - categories are defined on frontend
}

export interface SearchFilters {
    type: 'all' | 'configs' | 'mcp-servers' | 'prompts' | 'skills';
    agent: number | null;
    config_type: number | null;
    category: number | null;
    prompt_category: string | null;
    sort: 'relevance' | 'recent' | 'top';
}

export interface SearchPageProps {
    query: string;
    filters: SearchFilters;
    results: {
        configs: Config[];
        mcpServers: McpServer[];
        prompts: Prompt[];
        skills: Skill[];
    };
    counts: {
        configs: number;
        mcpServers: number;
        prompts: number;
        skills: number;
        total: number;
    };
    agents: Pick<Agent, 'id' | 'name' | 'slug'>[];
    configTypes: Pick<ConfigType, 'id' | 'name' | 'slug'>[];
    categories: Pick<Category, 'id' | 'name' | 'slug' | 'config_type_id'>[];
    promptCategories: string[];
}

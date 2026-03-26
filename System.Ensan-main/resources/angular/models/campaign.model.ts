export interface CampaignInfoItem {
    icon: string;
    label: string;
    value: string | number;
}

export type CampaignStatus = 'funding' | 'preparation' | 'completed' | 'upcoming';

export interface Campaign {
    id: number;
    title: string;
    description: string;
    imageUrl: string;
    status: CampaignStatus;
    statusLabel: string; // e.g., 'جاري التبرع', 'مكتملة'
    category?: string;
    categoryIcon?: string;

    // Progress Data
    progress?: number; // 0-100
    goalAmount?: number;
    collectedAmount?: number;
    beneficiariesCount?: number;

    // Dynamic Info Row
    quickStats: CampaignInfoItem[];

    // Action
    actionText: string;
    actionLink?: string; // Router link or external
    isActionDisabled?: boolean;
}

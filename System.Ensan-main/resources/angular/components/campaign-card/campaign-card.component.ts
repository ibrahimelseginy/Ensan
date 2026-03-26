import {
    Component,
    Input,
    Output,
    EventEmitter,
    ChangeDetectionStrategy,
    OnChanges,
    SimpleChanges
} from '@angular/core';
import { CommonModule } from '@angular/common';
import { Campaign, CampaignInfoItem } from '../../models/campaign.model';

@Component({
    selector: 'app-campaign-card',
    standalone: true,
    imports: [CommonModule],
    templateUrl: './campaign-card.component.html',
    styleUrls: ['./campaign-card.component.scss'],
    changeDetection: ChangeDetectionStrategy.OnPush
})
export class CampaignCardComponent implements OnChanges {
    @Input({ required: true }) campaign!: Campaign;
    @Output() actionClick = new EventEmitter<Campaign>();

    get hasProgressData(): boolean {
        return !!(this.campaign?.goalAmount && this.campaign?.collectedAmount);
    }

    get progressPercentage(): number {
        if (!this.campaign.goalAmount) return 0;
        const progress = (this.campaign.collectedAmount || 0) / this.campaign.goalAmount * 100;
        return Math.min(Math.round(progress), 100);
    }

    ngOnChanges(changes: SimpleChanges): void {
        // Perform any specific updates if needed
    }

    handleAction(): void {
        if (!this.campaign.isActionDisabled) {
            this.actionClick.emit(this.campaign);
        }
    }

    // Helper Methods for Dynamic Styling
    getStatusClass(status: string): string {
        switch (status) {
            case 'completed': return 'badge-success';
            case 'preparation': return 'badge-warning';
            case 'upcoming': return 'badge-info';
            default: return 'badge-primary'; // 'funding'
        }
    }

    getActionBtnClass(): string {
        if (this.campaign.isActionDisabled) return 'btn-disabled';
        return this.campaign.status === 'completed'
            ? 'btn-outline-success'
            : 'btn-primary-gradient';
    }

    getProgressColorClass(): string {
        const p = this.progressPercentage;
        if (p >= 100) return 'bg-success';
        if (p > 75) return 'bg-primary';
        if (p > 50) return 'bg-info';
        return 'bg-warning';
    }

    trackByStatLabel(index: number, item: CampaignInfoItem): string {
        return item.label;
    }
}

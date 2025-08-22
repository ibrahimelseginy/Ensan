import 'package:flutter/material.dart';
import 'package:gap/gap.dart';
import '../../core/colors.dart';
import '../../core/common_styles.dart';
import '../../core/constants/app_constants.dart';
import '../../components/common_widgets.dart';
import '../../core/data/app_data.dart';
import '../../core/models/service_model.dart';

class HomeScreen extends StatelessWidget {
  const HomeScreen({super.key});

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children: [
          _buildTopBanner(context),
          Expanded(
            child: SingleChildScrollView(
              padding: CommonStyles.mdPadding,
              child: Column(
                children: [
                  _buildPromotionalBanner(),
                  const Gap(AppConstants.lgPadding),
                  _buildHumanitarianCasesSection(),
                ],
              ),
            ),
          ),
          _buildBottomNavigation(),
        ],
      ),
    );
  }

  Widget _buildTopBanner(BuildContext context) {
    return Container(
      width: double.infinity,
      height: MediaQuery.of(context).size.height * 0.3,
      decoration: CommonStyles.primaryGradientDecoration,
      child: Padding(
        padding: CommonStyles.mdHPadding,
        child: Column(
          mainAxisAlignment: MainAxisAlignment.start,
          children: [
            const Gap(50),
            _buildWelcomeText(),
            const Gap(AppConstants.smPadding),
            _buildWelcomeSubtext(),
            _buildServicesRow(),
          ],
        ),
      ),
    );
  }

  Widget _buildWelcomeText() {
    return const Align(
      alignment: Alignment.centerRight,
      child: Text(
        'أهلاً عزيزي المتبرع',
        style: TextStyle(
          color: AppColors.white,
          fontSize: AppConstants.titleText,
          fontWeight: FontWeight.bold,
        ),
      ),
    );
  }

  Widget _buildWelcomeSubtext() {
    return const Align(
      alignment: Alignment.centerRight,
      child: Text(
        'مرحباً بك في انسان',
        style: TextStyle(color: AppColors.white, fontSize: AppConstants.lgText),
      ),
    );
  }

  Widget _buildServicesRow() {
    return CommonWidgets.horizontalScrollableRow(
      children: AppData.services
          .map(
            (service) => CommonWidgets.serviceIcon(
              icon: _getServiceIcon(service.name),
              label: service.shortName,
              color: service.color,
            ),
          )
          .toList(),
    );
  }

  IconData _getServiceIcon(String serviceName) {
    switch (serviceName) {
      case 'تطوير المجتمع':
        return Icons.handshake;
      case 'التعليم':
        return Icons.school;
      case 'الصحة':
        return Icons.favorite;
      case 'التكافل الاجتماعي':
        return Icons.people;
      case 'الإغاثة':
        return Icons.emergency;
      case 'الأيتام':
        return Icons.child_care;
      case 'المساجد':
        return Icons.mosque;
      case 'المياه':
        return Icons.water_drop;
      default:
        return Icons.help;
    }
  }

  Widget _buildPromotionalBanner() {
    return CommonWidgets.cardContainer(
      decoration: BoxDecoration(
        color: AppColors.secondaryLight.withOpacity(0.1),
        borderRadius: CommonStyles.lgRadius,
      ),
      child: Column(
        children: [
          _buildPromotionalIcons(),
          const Gap(AppConstants.mdPadding),
          _buildPromotionalText(),
          const Gap(AppConstants.mdPadding),
          _buildPromotionalTags(),
        ],
      ),
    );
  }

  Widget _buildPromotionalIcons() {
    final content = AppData.promotionalContent;
    return Row(
      mainAxisAlignment: MainAxisAlignment.center,
      children: List.generate(
        content['icons'].length,
        (index) => Padding(
          padding: const EdgeInsets.symmetric(
            horizontal: AppConstants.smPadding,
          ),
          child: Icon(
            content['icons'][index],
            color: content['iconColors'][index],
            size: AppConstants.xlIcon,
          ),
        ),
      ),
    );
  }

  Widget _buildPromotionalText() {
    final content = AppData.promotionalContent;
    return Column(
      children: [
        Text(
          content['title'],
          style: const TextStyle(
            color: AppColors.success,
            fontSize: AppConstants.mdText,
          ),
          textAlign: TextAlign.center,
        ),
        const Gap(AppConstants.smPadding),
        Text(
          content['subtitle'],
          style: const TextStyle(
            color: AppColors.success,
            fontSize: AppConstants.xlText,
            fontWeight: FontWeight.bold,
          ),
          textAlign: TextAlign.center,
        ),
      ],
    );
  }

  Widget _buildPromotionalTags() {
    return Wrap(
      spacing: AppConstants.smPadding,
      runSpacing: AppConstants.smPadding,
      children: AppData.commonTags
          .take(4)
          .map((tag) => CommonWidgets.tag(text: tag))
          .toList(),
    );
  }

  Widget _buildHumanitarianCasesSection() {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        CommonWidgets.sectionHeader(
          title: 'الحالات الإنسانية',
          titleStyle: CommonStyles.titleText,
        ),
        const Gap(AppConstants.mdPadding),
        _buildHumanitarianCaseCard(),
      ],
    );
  }

  Widget _buildHumanitarianCaseCard() {
    final caseData = AppData.humanitarianCases.first;

    return CommonWidgets.cardContainer(
      padding: EdgeInsets.zero,
      decoration: CommonStyles.elevatedCardDecoration,
      child: Column(
        children: [
          _buildCaseImage(caseData.imageUrl),
          _buildCaseContent(caseData),
        ],
      ),
    );
  }

  Widget _buildCaseImage(String imageUrl) {
    return Container(
      height: 150,
      decoration: BoxDecoration(
        image: DecorationImage(
          image: NetworkImage(imageUrl),
          fit: BoxFit.cover,
        ),
      ),
      child: Stack(
        children: [
          Positioned(
            right: AppConstants.mdPadding,
            bottom: AppConstants.mdPadding,
            child: Container(
              padding: CommonStyles.smPadding,
              decoration: BoxDecoration(
                color: AppColors.white.withOpacity(0.9),
                borderRadius: CommonStyles.xlRadius,
              ),
              child: const Icon(
                Icons.class_,
                color: AppColors.success,
                size: AppConstants.mdIcon,
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCaseContent(HumanitarianCaseModel caseData) {
    return Container(
      padding: CommonStyles.mdPadding,
      color: AppColors.white,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(caseData.title, style: CommonStyles.subtitleText),
          const Gap(AppConstants.smPadding),
          Text(caseData.description, style: CommonStyles.captionText),
        ],
      ),
    );
  }

  Widget _buildBottomNavigation() {
    return Container(
      padding: CommonStyles.mdVPadding,
      decoration: BoxDecoration(
        color: AppColors.white,
        boxShadow: CommonStyles.smShadow,
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
        children: AppData.navigationItems
            .map(
              (item) => CommonWidgets.navigationItem(
                icon: item['icon'],
                label: item['label'],
                isActive: item['isActive'],
              ),
            )
            .toList(),
      ),
    );
  }
}

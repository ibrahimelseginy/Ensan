import 'package:flutter/material.dart';
import '../models/service_model.dart';
import '../models/service_model.dart' as models;

class AppData {
  // Services Data
  static const List<ServiceModel> services = [
    ServiceModel(
      id: '1',
      name: 'تطوير المجتمع',
      shortName: 'تطوير الج...',
      description: 'برامج تطوير المجتمع المحلي',
      iconPath: 'assets/icons/community.png',
      color: Color(0xFFFF9800),
    ),
    ServiceModel(
      id: '2',
      name: 'التعليم',
      shortName: 'التعليم',
      description: 'برامج التعليم والتدريب',
      iconPath: 'assets/icons/education.png',
      color: Color(0xFF4CAF50),
    ),
    ServiceModel(
      id: '3',
      name: 'الصحة',
      shortName: 'الصحة',
      description: 'الرعاية الصحية والعلاج',
      iconPath: 'assets/icons/health.png',
      color: Color(0xFF4CAF50),
    ),
    ServiceModel(
      id: '4',
      name: 'التكافل الاجتماعي',
      shortName: 'التكافل ا...',
      description: 'برامج التكافل الاجتماعي',
      iconPath: 'assets/icons/social.png',
      color: Color(0xFFFF9800),
    ),
    ServiceModel(
      id: '5',
      name: 'الإغاثة',
      shortName: 'الإغاثة',
      description: 'برامج الإغاثة الطارئة',
      iconPath: 'assets/icons/relief.png',
      color: Color(0xFFFF9800),
    ),
    ServiceModel(
      id: '6',
      name: 'الأيتام',
      shortName: 'الأيتام',
      description: 'رعاية الأيتام',
      iconPath: 'assets/icons/orphans.png',
      color: Color(0xFFFF9800),
    ),
    ServiceModel(
      id: '7',
      name: 'المساجد',
      shortName: 'المساجد',
      description: 'بناء وصيانة المساجد',
      iconPath: 'assets/icons/mosque.png',
      color: Color(0xFFFF9800),
    ),
    ServiceModel(
      id: '8',
      name: 'المياه',
      shortName: 'المياه',
      description: 'مشاريع المياه',
      iconPath: 'assets/icons/water.png',
      color: Color(0xFFFF9800),
    ),
  ];

  // Humanitarian Cases Data
  static List<models.HumanitarianCaseModel> get humanitarianCases => [
    models.HumanitarianCaseModel(
      id: '1',
      title: 'ساند أهل غزة',
      description:
          'في ظل دعمنا الكامل لأهالينا في فلسطين، اتبرع لدعم غزة لتقديم المساعدات الإنسانية العاجلة',
      imageUrl:
          'https://via.placeholder.com/400x150/FF6B6B/FFFFFF?text=Gaza+Support',
      category: 'إغاثة',
      targetAmount: 1000000,
      currentAmount: 750000,
      donorsCount: 1250,
      deadline: DateTime(2024, 12, 31),
      isUrgent: true,
      tags: ['غزة', 'إغاثة', 'فلسطين'],
    ),
    models.HumanitarianCaseModel(
      id: '2',
      title: 'تعليم الأيتام',
      description: 'ساعد في تعليم الأيتام وتوفير المستلزمات الدراسية لهم',
      imageUrl:
          'https://via.placeholder.com/400x150/4CAF50/FFFFFF?text=Orphan+Education',
      category: 'تعليم',
      targetAmount: 500000,
      currentAmount: 300000,
      donorsCount: 800,
      deadline: DateTime(2024, 8, 31),
      isUrgent: false,
      tags: ['تعليم', 'أيتام', 'مستلزمات'],
    ),
  ];

  // Tags Data
  static const List<String> commonTags = [
    'غارمين',
    'تعليم',
    'صحة',
    'إطعام',
    'إغاثة',
    'أيتام',
    'مساجد',
    'مياه',
    'تطوير',
    'صحة',
  ];

  // Navigation Items Data
  static const List<Map<String, dynamic>> navigationItems = [
    {'icon': Icons.home, 'label': 'الرئيسية', 'isActive': true},
    {'icon': Icons.favorite, 'label': 'تبرع', 'isActive': true},
    {'icon': Icons.shopping_cart, 'label': 'سلة', 'isActive': false},
    {'icon': Icons.flash_on, 'label': 'حساب الزكاة', 'isActive': false},
    {'icon': Icons.grid_view, 'label': 'قائمة', 'isActive': false},
  ];

  // Promotional Content
  static const Map<String, dynamic> promotionalContent = {
    'title': 'كل باب خير وراه مصر الخير',
    'subtitle': 'زكاتك وصدقتك مع مصر الخير',
    'organization': 'مصر الخير',
    'icons': [Icons.person, Icons.medical_services, Icons.family_restroom],
    'iconColors': [Color(0xFF64B5F6), Color(0xFF4CAF50), Color(0xFFF06292)],
  };
}

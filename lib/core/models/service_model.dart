import 'package:flutter/material.dart';

class ServiceModel {
  final String id;
  final String name;
  final String shortName;
  final String description;
  final String iconPath;
  final Color color;
  final bool isActive;

  const ServiceModel({
    required this.id,
    required this.name,
    required this.shortName,
    required this.description,
    required this.iconPath,
    required this.color,
    this.isActive = true,
  });

  factory ServiceModel.fromJson(Map<String, dynamic> json) {
    return ServiceModel(
      id: json['id'] ?? '',
      name: json['name'] ?? '',
      shortName: json['shortName'] ?? '',
      description: json['description'] ?? '',
      iconPath: json['iconPath'] ?? '',
      color: _parseColor(json['color'] ?? '#FF9800'),
      isActive: json['isActive'] ?? true,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'shortName': shortName,
      'description': description,
      'iconPath': iconPath,
      'color': color.value.toRadixString(16),
      'isActive': isActive,
    };
  }

  static Color _parseColor(String colorString) {
    try {
      if (colorString.startsWith('#')) {
        colorString = colorString.substring(1);
      }
      if (colorString.length == 6) {
        colorString = 'FF$colorString';
      }
      return Color(int.parse(colorString, radix: 16));
    } catch (e) {
      return const Color(0xFFFF9800); // Default orange color
    }
  }
}

class HumanitarianCaseModel {
  final String id;
  final String title;
  final String description;
  final String imageUrl;
  final String category;
  final double targetAmount;
  final double currentAmount;
  final int donorsCount;
  final DateTime deadline;
  final bool isUrgent;
  final List<String> tags;

  const HumanitarianCaseModel({
    required this.id,
    required this.title,
    required this.description,
    required this.imageUrl,
    required this.category,
    required this.targetAmount,
    required this.currentAmount,
    required this.donorsCount,
    required this.deadline,
    this.isUrgent = false,
    this.tags = const [],
  });

  factory HumanitarianCaseModel.fromJson(Map<String, dynamic> json) {
    return HumanitarianCaseModel(
      id: json['id'] ?? '',
      title: json['title'] ?? '',
      description: json['description'] ?? '',
      imageUrl: json['imageUrl'] ?? '',
      category: json['category'] ?? '',
      targetAmount: (json['targetAmount'] ?? 0.0).toDouble(),
      currentAmount: (json['currentAmount'] ?? 0.0).toDouble(),
      donorsCount: json['donorsCount'] ?? 0,
      deadline: DateTime.tryParse(json['deadline'] ?? '') ?? DateTime.now(),
      isUrgent: json['isUrgent'] ?? false,
      tags: List<String>.from(json['tags'] ?? []),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'title': title,
      'description': description,
      'imageUrl': imageUrl,
      'category': category,
      'targetAmount': targetAmount,
      'currentAmount': currentAmount,
      'donorsCount': donorsCount,
      'deadline': deadline.toIso8601String(),
      'isUrgent': isUrgent,
      'tags': tags,
    };
  }

  double get progressPercentage =>
      targetAmount > 0 ? (currentAmount / targetAmount) * 100 : 0;

  bool get isCompleted => currentAmount >= targetAmount;
  bool get isExpired => DateTime.now().isAfter(deadline);
}

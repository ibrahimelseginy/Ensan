import 'package:flutter/material.dart';
import 'package:flutter/foundation.dart';
import 'package:device_preview/device_preview.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'router/app_router.dart';
import 'package:provider/provider.dart';
import 'auth/cubit/register_cubit.dart';

void main() {
  runApp(
    DevicePreview(
      enabled: !kReleaseMode,
      builder: (context) => ChangeNotifierProvider(
        create: (_) => RegisterCubit(),
        child: const EnsanApp(),
      ),
    ),
  );
}

class EnsanApp extends StatelessWidget {
  const EnsanApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Ensan',
      debugShowCheckedModeBanner: false,
      locale: const Locale('ar', 'SA'),
      supportedLocales: const [
        Locale('ar', 'SA'),
        Locale('en', 'US'),
      ],
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      routes: AppRouter.routes,
      initialRoute: AppRouter.homeScreen,
      theme: ThemeData(
        primarySwatch: Colors.green,
        fontFamily: 'Cairo',
        useMaterial3: true,
      ),
    );
  }
}

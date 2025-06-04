import { Routes } from "@angular/router";
import { HomeComponent } from "./components/home/home.component";
import { LoginFormComponent } from "./components/login-form/login-form.component";
import { SignupFormComponent } from "./components/signup-form/signup-form.component";
import { ProfileComponent } from "./components/profile/profile.component";
import { AuthGuard } from "../guards/auth.guard";
import { SessionGuard } from "../guards/session.guard";
import { DashboardComponent } from "./components/dashboard/dashboard.component";
import { DASHBOARD_ROUTE, LOGIN_ROUTE, PROFILE_ROUTE, SIGNUP_ROUTE } from "../constants/route.constants";

export const routes: Routes = [
  { path: "", component: HomeComponent, pathMatch: "full" },
  { path: LOGIN_ROUTE, component: LoginFormComponent, pathMatch: "full", canActivate: [] },
  { path: SIGNUP_ROUTE, component: SignupFormComponent, pathMatch: "full", canActivate: [SessionGuard] },
  { path: PROFILE_ROUTE, component: ProfileComponent, pathMatch: "full", canActivate: [AuthGuard] },
  { path: DASHBOARD_ROUTE, component: DashboardComponent, pathMatch: "full", canActivate: [AuthGuard] },
];

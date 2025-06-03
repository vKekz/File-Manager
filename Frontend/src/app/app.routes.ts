import { Routes } from "@angular/router";
import { HomeComponent } from "./components/home/home.component";
import { LoginFormComponent } from "./components/login-form/login-form.component";
import { SignupFormComponent } from "./components/signup-form/signup-form.component";
import { ProfileComponent } from "./components/profile/profile.component";
import { AuthGuard } from "../guards/auth.guard";
import { ValidateGuard } from "../guards/validate.guard";
import { SessionGuard } from "../guards/session.guard";

export const routes: Routes = [
  { path: "", component: HomeComponent, pathMatch: "full", canActivate: [ValidateGuard] },
  { path: "login", component: LoginFormComponent, pathMatch: "full", canActivate: [ValidateGuard, SessionGuard] },
  { path: "signup", component: SignupFormComponent, pathMatch: "full", canActivate: [ValidateGuard, SessionGuard] },
  { path: "profile", component: ProfileComponent, pathMatch: "full", canActivate: [ValidateGuard, AuthGuard] },
];

import { Routes } from "@angular/router";
import { HomeComponent } from "./components/home/home.component";
import { LoginFormComponent } from "./components/login-form/login-form.component";
import { SignupFormComponent } from "./components/signup-form/signup-form.component";
import { ProfileComponent } from "./components/profile/profile.component";
import { AuthGuard } from "../guards/auth.guard";
import { SessionGuard } from "../guards/session.guard";
import { StorageComponent } from "./components/storage/storage.component";
import { APP_ROUTES } from "../constants/route.constants";

export const routes: Routes = [
  { path: "", component: HomeComponent, pathMatch: "full" },
  { path: APP_ROUTES.login, component: LoginFormComponent, pathMatch: "full", canActivate: [] },
  { path: APP_ROUTES.signup, component: SignupFormComponent, pathMatch: "full", canActivate: [SessionGuard] },
  { path: APP_ROUTES.profile, component: ProfileComponent, pathMatch: "full", canActivate: [AuthGuard] },
  { path: APP_ROUTES.storage, component: StorageComponent, pathMatch: "full", canActivate: [AuthGuard] },
];
